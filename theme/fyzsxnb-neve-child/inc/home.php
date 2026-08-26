<?php
/**
 * Homepage controller support — locale config, URL resolvers, data access.
 *
 * UI V2 0.3.5: the EN/RU homepages share ONE template structure driven by
 * per-locale config. Rendering is controlled by front-page.php (EN front
 * page) and page.php's 'ru' branch (RU home). The legacy page Custom HTML
 * is retired; this file is the single source of homepage presentation.
 *
 * Hard rule: internal article/desk/category URLs are produced by resolvers
 * (get_permalink / get_category_link / home_url), never hardcoded absolute.
 *
 * @package FYZSXNB_Neve_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage locale (reuses the existing language detection).
 *
 * @return string 'en-US' | 'ru-RU'
 */
function fyzsxnb_home_locale() {
	return fyzsxnb_is_russian_view() ? 'ru-RU' : 'en-US';
}

/**
 * Resolve a target spec to an internal URL. Returns null when not resolvable.
 *
 * @param array $spec Spec with 'type' and its key ('path', 'slug', 'name', 'anchor').
 * @return string|null
 */
function fyzsxnb_home_target( $spec ) {
	if ( ! is_array( $spec ) ) {
		return null;
	}
	$type = isset( $spec['type'] ) ? $spec['type'] : '';
	switch ( $type ) {
		case 'path':
			return home_url( isset( $spec['path'] ) ? (string) $spec['path'] : '/' );
		case 'category':
			$term = get_term_by( 'slug', isset( $spec['slug'] ) ? (string) $spec['slug'] : '', 'category' );
			return ( $term && ! is_wp_error( $term ) ) ? get_category_link( $term ) : null;
		case 'page':
			$page = get_page_by_path( isset( $spec['name'] ) ? (string) $spec['name'] : '' );
			return $page ? get_permalink( $page ) : null;
		case 'post':
			$slug = isset( $spec['slug'] ) ? (string) $spec['slug'] : '';
			$found = get_posts( array( 'name' => $slug, 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 1 ) );
			return ! empty( $found ) ? get_permalink( $found[0] ) : null;
		case 'anchor':
			return isset( $spec['anchor'] ) ? '#' . ltrim( (string) $spec['anchor'], '#' ) : null;
		default:
			return null;
	}
}

/**
 * Featured image URL helper (returns '' when none).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function fyzsxnb_home_image( $post_id ) {
	$thumb = get_the_post_thumbnail_url( $post_id, 'large' );
	return $thumb ? $thumb : '';
}

/**
 * Latest homepage feeds via the feeds plugin backend (migration of call, not
 * of algorithm — 0.3.5 keeps the plugin untouched).
 *
 * @param string $locale Locale.
 * @param string $type   'signals' | 'guides'.
 * @return WP_Post[]
 */
function fyzsxnb_home_latest_posts( $locale, $type ) {
	if ( ! function_exists( 'fyzsxnb_get_home_feed_posts' ) ) {
		return array();
	}
	$limit = 'guides' === $type ? 6 : 4;
	$exclude = array();
	if ( 'guides' === $type ) {
		$exclude = wp_list_pluck( fyzsxnb_get_home_feed_posts( $locale, 'signals', 4 ), 'ID' );
	}
	$posts = fyzsxnb_get_home_feed_posts( $locale, $type, $limit, $exclude );
	if ( ! is_array( $posts ) ) {
		return array();
	}
	return array_slice( $posts, 0, $limit ); // graceful: fewer results show fewer
}

/**
 * Render a home feed block (signals/guides) using the plugin renderer.
 *
 * @param string $locale Locale.
 * @param string $type   Feed type.
 * @return string
 */
function fyzsxnb_home_feed_html( $locale, $type ) {
	if ( ! function_exists( 'fyzsxnb_render_home_feed' ) ) {
		return '';
	}
	$posts = fyzsxnb_home_latest_posts( $locale, $type );
	if ( empty( $posts ) ) {
		return '';
	}
	$html = fyzsxnb_render_home_feed( $posts, $locale, $type );
	return is_string( $html ) ? $html : '';
}

/**
 * Homepage locale config. Single source for copy; EN/RU share the structure.
 *
 * @param string $locale Locale.
 * @return array
 */
function fyzsxnb_home_config( $locale ) {
	$config = array();
	if ( 'ru-RU' === $locale ) {
		$config['lang'] = 'ru';
		$config['nav'] = array(
			array( 'label' => 'Последние', 'spec' => array( 'type' => 'anchor', 'anchor' => 'latest' ) ),
			array( 'label' => 'Темы', 'spec' => array( 'type' => 'anchor', 'anchor' => 'topics' ) ),
			array( 'label' => 'Методика', 'spec' => array( 'type' => 'anchor', 'anchor' => 'method' ) ),
			array( 'label' => 'О проекте', 'spec' => array( 'type' => 'page', 'name' => 'about-fyzsxnb' ) ),
		);
		$config['lang_switch'] = array( 'label' => 'EN', 'url' => home_url( '/' ), 'hreflang' => 'en', 'current' => false );
		$config['hero'] = array(
			'eyebrow' => 'Китайские технологии и товары без пересказов',
			'title'   => 'Китайские технологии и товары: исследования для России',
			'deck'    => 'Мы изучаем китайские первоисточники, технические сообщества, документацию производителей и реальные устройства, чтобы находить сведения, которых ещё нет на русскоязычных сайтах. Главное здесь — совместимость с Россией, различия версий, практическая настройка и проверяемые факты.',
			'promise' => array(
				array( 'strong' => 'Китайские первоисточники', 'span' => 'Документы, локальные модели, инженерные разборы и сигналы от производителей.' ),
				array( 'strong' => 'Проверка для России', 'span' => 'Частоты, прошивки, приложения, гарантия, цена, доставка и ограничения.' ),
				array( 'strong' => 'Без машинного перевода', 'span' => 'Каждый материал заново собран для русскоязычного читателя и снабжён источниками.' ),
			),
		);
		$config['signals'] = array(
			'section_id' => 'latest',
			'title'      => 'Последние исследования',
			'note'       => 'Материалы обновляются, когда появляются новые прошивки, документы, цены или подтверждённый пользовательский опыт.',
		);
		$config['featured'] = null; // RU keeps editorial desks, not a featured block
		$config['desks'] = array(
			array( 'meta' => 'Авто из Китая', 'title' => 'Автомобили из Китая в России', 'p' => 'Импорт новых и б/у авто, проверка ЭПТС по VIN, утильсбор 2026, комплектации и русификация.', 'cta' => 'Открыть раздел авто', 'spec' => array( 'type' => 'path', 'path' => '/ru/cars-from-china/' ) ),
			array( 'meta' => 'База ремонта', 'title' => 'База знаний по ремонту', 'p' => 'Ремонт DQ381 (P1735/P1736), запчасти DKV/DPL, прожиг GPF, прошивка Chery и CAN-шины.', 'cta' => 'Открыть базу знаний', 'spec' => array( 'type' => 'path', 'path' => '/ru/repair-knowledge-base/' ) ),
			array( 'meta' => 'Промкомпоненты', 'title' => 'Промышленные компоненты и автоматизация', 'p' => 'Датчики давления 4-20мА, ПЛК, преобразователи частоты и замена ушедших брендов.', 'cta' => 'Перейти к компонентам', 'spec' => array( 'type' => 'path', 'path' => '/ru/industrial-supply-chain/' ) ),
			array( 'meta' => 'Биомедицина', 'title' => 'Биомедицина и регуляторика', 'p' => 'Регистрация FDA 21 CFR 207, маркировка NMPA UDI 2027 и молекулярные POCT-системы.', 'cta' => 'Открыть раздел регуляторики', 'spec' => array( 'type' => 'path', 'path' => '/ru/biomed-regulatory/' ) ),
		);
		$config['guides'] = array(
			'section_id' => 'guides',
			'eyebrow'    => 'Библиотека решений',
			'title'      => 'Последние руководства',
			'browse'     => null,
		);
		$config['trust'] = array(
			'section_id' => 'method',
			'eyebrow'    => 'Методика',
			'title'      => 'Как мы проверяем сведения',
			'method'     => 'Сначала фиксируем китайский первоисточник, затем проверяем модель, дату и технические параметры по официальной документации. После этого сопоставляем данные с российскими сетями, правилами, сервисами, ценами и пользовательским опытом. Слухи и сообщения сообществ обозначаются как неподтверждённые, а не выдаются за факт.',
			'notice'     => 'Материалы о биомедицине и медицинских технологиях предназначены для отраслевого и информационного анализа. Они не являются медицинской консультацией, диагнозом, назначением лечения или инвестиционной рекомендацией.',
			'steps'      => array(),
		);
		$config['cta'] = array(
			'title' => 'Нужно проверить деталь, модель или поставщика?',
			'p'     => 'Пришлите номер детали, фото или документацию.',
			'label' => 'Связаться',
			'spec'  => array( 'type' => 'page', 'name' => 'contact' ),
		);
		$config['reading'] = null;
	} else {
		$config['lang'] = 'en';
		$config['nav'] = array(
			array( 'label' => 'Latest', 'spec' => array( 'type' => 'path', 'path' => '/blog/' ) ),
			array( 'label' => 'China Tech', 'spec' => array( 'type' => 'category', 'slug' => 'china-tech-products' ) ),
			array( 'label' => 'Biomed', 'spec' => array( 'type' => 'category', 'slug' => 'china-global-biomed' ) ),
			array( 'label' => 'Product Signals', 'spec' => array( 'type' => 'category', 'slug' => 'product-opportunity-research' ) ),
			array( 'label' => 'About', 'spec' => array( 'type' => 'page', 'name' => 'about-fyzsxnb' ) ),
		);
		$config['lang_switch'] = array( 'label' => 'RU', 'url' => home_url( '/ru/' ), 'hreflang' => 'ru', 'current' => false );
		$config['hero'] = array(
			'eyebrow' => 'Cross-Border Industrial & Automotive Intelligence',
			'title'   => 'Actionable Technical Insights from China to Global Markets.',
			'deck'    => 'Technical teardowns, cross-border diagnostic standards, regulatory roadmaps, and verified supply chain intelligence to empower international business decisions.',
			'topic_links' => array(
				array( 'label' => 'Russia Auto Solutions', 'spec' => array( 'type' => 'path', 'path' => '/ru/' ) ),
				array( 'label' => 'Biomedical Intelligence', 'spec' => array( 'type' => 'category', 'slug' => 'china-global-biomed' ) ),
				array( 'label' => 'Industrial Supply Chain', 'spec' => array( 'type' => 'category', 'slug' => 'china-tech-products' ) ),
				array( 'label' => 'Hardware & Equipment', 'spec' => array( 'type' => 'category', 'slug' => 'product-opportunity-research' ) ),
			),
			'hero_story' => array(
				'spec'  => array( 'type' => 'post', 'slug' => 'fully-automated-molecular-poct-system-ifind-procurement-guide' ),
				'meta'  => 'EN · Biomed · Procurement guide',
				'title' => 'How to shortlist a molecular POCT system from China',
				'p'     => 'Comprehensive analysis of analytical throughput, cartridge assay menus, regulatory standards, and practical procurement specifications.',
			),
		);
		$config['signals'] = array(
			'section_id' => 'latest',
			'title'      => 'Latest Intelligence Reports',
			'note'       => 'Real-world operational telemetry, engineering analysis, and direct supplier intelligence across key cross-border sectors.',
		);
		$config['featured'] = array(
			'section_id' => 'featured',
			'eyebrow'    => 'Strategic Research',
			'title'      => 'Featured Industry Reports',
			'note'       => 'Comprehensive technical breakdowns and verified manufacturer data delivering immediate decision value for engineers, clinicians, and procurement directors.',
			'cards' => array(
				array( 'kind' => 'lead', 'label' => 'EN · Biomed procurement', 'slug' => 'fully-automated-molecular-poct-system-ifind-procurement-guide', 'p' => 'A buyer-oriented map of workflow, throughput, assay availability and evidence requests.', 'cta' => 'Read procurement guide' ),
				array( 'kind' => 'small', 'label' => 'EN · Pet care', 'slug' => 'microplastics-in-pet-food-study-methods-limits', 'p' => 'Five studies compared without turning detection into an unsupported health claim.', 'cta' => 'View Report' ),
				array( 'kind' => 'small', 'label' => 'EN · Product research', 'slug' => 'ai-voice-recorder-buying-guide-subscription-privacy-offline', 'p' => 'Subscription cost, privacy and offline limits across five real workflows.', 'cta' => 'Open comparison' ),
				array( 'kind' => 'compact', 'label' => 'EN · Biomed', 'slug' => 'ifind-tbr-mtb-rif-cartridge-procurement-guide', 'p' => 'Analytical claims, direct evidence and procurement questions.', 'cta' => 'View Report' ),
				array( 'kind' => 'compact', 'label' => 'EN · Biomed', 'slug' => 'ifind-ifq-inh-fluoroquinolone-resistance-cartridge-guide', 'p' => 'What the brochure says and what a buyer should still request.', 'cta' => 'Read guide' ),
				array( 'kind' => 'compact', 'label' => 'EN · Laboratory Diagnostics', 'slug' => 'tb-molecular-test-lod-cfu-10-vs-100', 'p' => 'Why a lower number is not a complete performance claim.', 'cta' => 'Understand LoD' ),
			),
		);
		$config['desks'] = array(
			array( 'meta' => 'Automotive Hub', 'title' => 'Cars From China in Russia', 'p' => 'New & used car imports, VIN & EPTS verification, 2026 recycling fee, winter adaptation and spare parts.', 'cta' => 'Open Auto Hub', 'spec' => array( 'type' => 'path', 'path' => '/cars-from-china/' ) ),
			array( 'meta' => 'Repair Database', 'title' => 'Auto Repair Knowledge Base', 'p' => 'DQ381 mechatronic sensor fixes (P1735), EA888 DKV parts, GPF regeneration and firmware mods.', 'cta' => 'Explore Repair Database', 'spec' => array( 'type' => 'path', 'path' => '/repair-knowledge-base/' ) ),
			array( 'meta' => 'Industrial Supply', 'title' => 'China Industrial Supply Chain', 'p' => '4-20mA pressure transmitters, VFD Modbus wiring, M22 fluid adapters and PLC alternatives.', 'cta' => 'Explore Industrial Hub', 'spec' => array( 'type' => 'path', 'path' => '/industrial-supply-chain/' ) ),
			array( 'meta' => 'Biomed & Regulation', 'title' => 'Biomed & Regulatory Intelligence', 'p' => 'FDA 21 CFR 207 foreign establishment registration, NMPA UDI 2027 and molecular POCT procurement.', 'cta' => 'Explore Medical Hub', 'spec' => array( 'type' => 'path', 'path' => '/biomed-regulatory/' ) ),
		);
		$config['guides'] = array(
			'section_id' => 'guides',
			'eyebrow'    => 'Knowledge Base',
			'title'      => 'Technical & Procurement Guides',
			'browse'     => array( 'label' => 'Browse All Reports', 'spec' => array( 'type' => 'path', 'path' => '/blog/' ) ),
		);
		$config['trust'] = array(
			'section_id' => 'trust',
			'eyebrow'    => 'Why Industry Leaders Rely on FYZSXNB',
			'title'      => 'Verified Data. Direct Sources. Actionable Solutions.',
			'intro'      => 'We eliminate information asymmetry by providing direct access to Chinese engineering documentation, real operational benchmarks, and verified B2B supply lines.',
			'steps'      => array(
				array( 's' => 'Discover.', 'd' => 'Access original engineering data, firmware architectures, and real-world failure modes directly from domestic Chinese ecosystems.' ),
				array( 's' => 'Validate.', 'd' => 'Cross-examine technical specifications against international regulatory standards, laboratory thresholds, and climate demands.' ),
				array( 's' => 'Clarify.', 'd' => 'Obtain clear, unbiased technical frameworks that distinguish proven performance from marketing claims.' ),
				array( 's' => 'Connect.', 'd' => 'Directly bridge actionable technical demand with verified Chinese Tier-1 suppliers and specialized repair supply chains.' ),
			),
			'notice'     => '',
		);
		$config['cta'] = array(
			'title' => 'Need Specialized Technical Research or Component Sourcing?',
			'p'     => 'Inquire about specific vehicle architectures, laboratory instrumentation, or industrial supply channels to receive direct technical intelligence.',
			'label' => 'Contact Industry Analysts',
			'spec'  => array( 'type' => 'page', 'name' => 'contact' ),
		);
		$config['reading'] = array(
			'section_id' => 'reading',
			'eyebrow'    => 'Deep-Dive Archive',
			'title'      => 'Cross-Border Technical Knowledge Base',
			'cards'      => array(
				array( 'slug' => 'ifind-tbr-mtb-rif-cartridge-procurement-guide', 'label' => 'EN · Biomed', 'p' => 'A cross-border laboratory decision guide.', 'document' => true ),
				array( 'slug' => 'microplastics-in-pet-food-study-methods-limits', 'label' => 'EN · Pet care', 'p' => 'Methods, findings and the limits of health interpretation.', 'document' => false ),
				array( 'slug' => 'ai-voice-recorder-buying-guide-subscription-privacy-offline', 'label' => 'EN · Product research', 'p' => 'A practical comparison built around real operating constraints.', 'document' => false ),
			),
			'links'      => array(
				array( 'label' => 'Pet care archive', 'spec' => array( 'type' => 'category', 'slug' => 'pet-care-products' ) ),
				array( 'label' => 'All articles', 'spec' => array( 'type' => 'path', 'path' => '/blog/' ) ),
				array( 'label' => 'About FYZSXNB', 'spec' => array( 'type' => 'page', 'name' => 'about-fyzsxnb' ) ),
			),
		);
	}
	return $config;
}

/**
 * Render the full homepage (EN front page or RU page 400). Shared structure.
 *
 * @param string $locale Locale.
 */
/**
 * The RU home page (400) has "disable footer" set in its page settings;
 * the migrated homepage needs the unified footer, so force content parts on.
 */
add_filter(
	'neve_filter_toggle_content_parts',
	function ( $show, $layout ) {
		if ( is_page( 'ru' ) ) {
			return true;
		}
		return $show;
	},
	200,
	2
);


add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_page( 'ru' ) ) {
			$classes = array_values( array_diff( $classes, array( 'nv-without-footer' ) ) );
		}
		return $classes;
	},
	200
);

function fyzsxnb_render_homepage( $locale ) {
	$cfg = fyzsxnb_home_config( $locale );
	get_header();
	echo '<div class="fyz-home" lang="' . esc_attr( $cfg['lang'] ) . '">';
	get_template_part( 'template-parts/home/hero', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	get_template_part( 'template-parts/home/signals', null, array( 'locale' => $locale, 'cfg' => $cfg, 'signals' => $cfg['signals'] ) );
	if ( ! empty( $cfg['featured'] ) ) {
		get_template_part( 'template-parts/home/featured', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	}
	if ( ! empty( $cfg['desks'] ) ) {
		get_template_part( 'template-parts/home/desks', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	}
	get_template_part( 'template-parts/home/guides', null, array( 'locale' => $locale, 'cfg' => $cfg, 'guides' => $cfg['guides'] ) );
	if ( ! empty( $cfg['trust'] ) ) {
		get_template_part( 'template-parts/home/trust', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	}
	if ( ! empty( $cfg['cta'] ) ) {
		get_template_part( 'template-parts/home/cta', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	}
	if ( ! empty( $cfg['reading'] ) ) {
		get_template_part( 'template-parts/home/reading', null, array( 'locale' => $locale, 'cfg' => $cfg ) );
	}
	echo '</div>';
	get_footer();
}