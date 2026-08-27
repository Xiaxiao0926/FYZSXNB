# FYZSXNB Feature Flag Architecture — `FYZ_USE_RESOLVER_V2`

**Document ID:** `FYZ-DOC-20260820-FEATURE-FLAG-045C2`  
**Stage:** `0.4.5-C2`  
**Author:** Google Gemini Flash 3.7  
**Scope:** Soft-Switch Specification, Dispatcher Implementation, and Lifecycle Management  

---

## 1. Feature Flag 定义与取值规范 (Specification)

```php
/**
 * Language Contract V2 Resolver Feature Flag.
 *
 * Values:
 *   false: Execute Legacy Resolver (fyzsxnb_get_russian_post_ids + Category 54).
 *   true:  Execute Resolver V2 (Metadata-first + structural validation + safe fallback).
 *
 * Default: false (Guarantees 100% zero-regression baseline upon deployment).
 */
if ( ! defined( 'FYZ_USE_RESOLVER_V2' ) ) {
	$env_flag = getenv( 'FYZ_USE_RESOLVER_V2' );
	if ( false !== $env_flag ) {
		define( 'FYZ_USE_RESOLVER_V2', in_array( strtolower( trim( (string) $env_flag ) ), array( '1', 'true', 'on', 'yes' ), true ) );
	} else {
		define( 'FYZ_USE_RESOLVER_V2', false );
	}
}
```

---

## 2. 分发器设计与双轨架构 (Dispatcher Architecture)

通过轻量分发器（Dispatcher）实现主逻辑与具体解析器解耦：

```php
/**
 * Language attributes filter dispatcher.
 */
function fyzsxnb_filter_language_attributes( $output ) {
	if ( is_admin() ) {
		return $output;
	}

	if ( defined( 'FYZ_USE_RESOLVER_V2' ) && FYZ_USE_RESOLVER_V2 ) {
		// V2 路径: 支持 en, ru, zh
		$resolved = fyzsxnb_resolve_content_locale( get_queried_object_id() );
		if ( 'ru' === $resolved['locale'] ) {
			return preg_replace( '/lang="[^"]*"/', 'lang="ru-RU"', $output );
		} elseif ( 'zh' === $resolved['locale'] ) {
			return preg_replace( '/lang="[^"]*"/', 'lang="zh-CN"', $output );
		}
		return $output;
	}

	// Legacy 路径: 仅区分 Russian Target 与 Default
	if ( fyzsxnb_is_russian_target() ) {
		return preg_replace( '/lang="[^"]*"/', 'lang="ru-RU"', $output );
	}
	return $output;
}
```

---

## 3. 开关生命周期管理 (Lifecycle Management)

```text
[Phase 1: 影子就绪 (Shadow Ready)]
  - 默认 Flag = false
  - 生产运行 Legacy 逻辑，内部记录 V2 解析日志
         │
         ▼
[Phase 2: 内部验证 (Canary / QA)]
  - QA 环境或特定请求携带测试 Header 强制激活 V2 路径
  - 运行 31 项集成测试与 30 篇重点样本比对
         │
         ▼
[Phase 3: 生产全量生效 (Public Live)]
  - 生产配置修改为 FYZ_USE_RESOLVER_V2 = true
  - 公网全面启用 V2 解析器
         │
         ▼
[Phase 4: 稳定退役 (Flag Retirement)]
  - 生产持续稳定运行 30 天且 0 故障后，将 Legacy 分支代码安全移除
```
