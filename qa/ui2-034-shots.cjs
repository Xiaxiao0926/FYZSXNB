// ui2-034-shots.cjs — before/after for 0.3.4 Article/Desk/Archive V2.
// Covers: 4 article types (EN long / RU long-title / table / heading-heavy),
// archive, desk (category), CFC brand/model, JS-on/JS-off, TOC metrics.
const fs = require('fs');
const path = require('path');
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');

const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
const outDir = process.argv[2] || path.join(__dirname, 'screenshots', 'ui2-034');
const stage = process.argv[3] || 'shot';
const JS_OFF = process.argv[4] === 'jsoff';
fs.mkdirSync(path.join(outDir, stage), { recursive: true });

const TARGETS = [
  ['en-long-1440', 'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', 1440, 900],
  ['en-long-390', 'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', 390, 844],
  ['ru-longtitle-1440', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/', 1440, 900],
  ['ru-longtitle-390', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/', 390, 844],
  ['en-table-1440', 'https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts/', 1440, 900],
  ['en-table-390', 'https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts/', 390, 844],
  ['ru-article2-1440', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev/', 1440, 900],
  ['ru-article2-390', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev/', 390, 844],
  ['archive-ru-1440', 'https://fyzsxnb.com/category/russian-library/', 1440, 900],
  ['desk-en-1440', 'https://fyzsxnb.com/category/china-tech-products/', 1440, 900],
  ['cfc-brand-1440', 'https://fyzsxnb.com/ru/cars-from-china/volkswagen/', 1440, 900],
  ['cfc-model-1440', 'https://fyzsxnb.com/ru/cars-from-china/volkswagen/tayron/', 1440, 900],
  ['home-en-390', 'https://fyzsxnb.com/', 390, 844],
];

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const results = {};
  for (const [name, url, w, h] of TARGETS) {
    const page = await browser.newPage({ viewport: { width: w, height: h } });
    const errors = [];
    const assets = [];
    page.on('pageerror', (e) => errors.push(String(e)));
    page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
    page.on('response', (r) => {
      const ct = r.headers()['content-type'] || '';
      if (ct.includes('font') || ct.includes('javascript') || ct.includes('stylesheet')) {
        assets.push(r.url().split('/').pop());
      }
    });
    if (JS_OFF) {
      // Simulate JS being unavailable: the inline TOC IIFE bails on the loaded flag.
      await page.addInitScript(() => { window.__fyzTocLoaded = true; });
    }
    await page.addInitScript(() => {
      window.__fyzVitals = { cls: 0 };
      try {
        new PerformanceObserver((list) => {
          for (const e of list.getEntries()) {
            if (!e.hadRecentInput) window.__fyzVitals.cls += e.value || 0;
          }
        }).observe({ type: 'layout-shift', buffered: true });
      } catch { /* ignore */ }
    });
    try {
      await page.goto(url + (url.includes('?') ? '&' : '?') + 'x=034', { waitUntil: 'load', timeout: 60000 });
      // LiteSpeed defers JS until user interaction (scroll/click) — simulate it.
      await page.mouse.move(80, 120).catch(() => {});
      await page.mouse.wheel(0, 200).catch(() => {});
      await page.mouse.down().catch(() => {});
      await page.mouse.up().catch(() => {});
      await page.waitForTimeout(1200);
      await page.screenshot({ path: path.join(outDir, stage, (JS_OFF ? 'jsoff-' : '') + name + '.png'), fullPage: true });
      const m = await page.evaluate(() => {
        const h1 = document.querySelector('h1');
        const toc = document.querySelector('.fyz-article-toc');
        const tocLinks = toc ? toc.querySelectorAll('a[href^="#"]').length : 0;
        const ids = Array.prototype.slice.call(document.querySelectorAll('[id]')).map((e) => e.id);
        const dupIds = ids.filter((id, i) => ids.indexOf(id) !== i);
        return {
          h1: document.querySelectorAll('h1').length,
          h1Len: h1 ? h1.textContent.length : 0,
          h1Font: h1 ? getComputedStyle(h1).fontFamily : null,
          toc: !!toc,
          tocLinks,
          tocSummary: toc ? (toc.querySelector('summary') || {}).textContent : null,
          dupAnchors: dupIds.length,
          overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
          gridCols: (() => { const w = document.querySelector('.nv-single-post-wrap.col'); return w ? getComputedStyle(w).gridTemplateColumns : null; })(),
          metaVisible: (() => { const nv = document.querySelector('.nv-meta-list'); return nv ? getComputedStyle(nv).display : 'n/a'; })(),
          related: document.querySelectorAll('.fyz-related__item').length,
          cta: document.querySelectorAll('.fyz-research-cta').length,
          vitals: window.__fyzVitals || { cls: 0 },
        };
      });
      results[name] = { ok: true, ...m, consoleErrors: errors.length };
    } catch (e) {
      results[name] = { ok: false, error: String(e).slice(0, 140) };
    }
    await page.close();
  }
  await browser.close();
  console.log(JSON.stringify(results, null, 2));
  const bad = Object.values(results).filter((r) => !r.ok || r.overflow || r.h1 !== 1 || r.consoleErrors > 0);
  process.exit(bad.length ? 1 : 0);
})().catch((e) => { console.error(e); process.exit(2); });