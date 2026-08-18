// ui2-033-shots.cjs — before/after screenshots + font/network/CLS/LCP metrics for 0.3.3.
const fs = require('fs');
const path = require('path');
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');

const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
const outDir = process.argv[2] || path.join(__dirname, 'screenshots', 'ui2-033');
const stage = process.argv[3] || 'shot';
fs.mkdirSync(path.join(outDir, stage), { recursive: true });

const TARGETS = [
  ['home-en-1440', 'https://fyzsxnb.com/', 1440, 900],
  ['home-en-390', 'https://fyzsxnb.com/', 390, 844],
  ['home-ru-1440', 'https://fyzsxnb.com/ru/', 1440, 900],
  ['home-ru-390', 'https://fyzsxnb.com/ru/', 390, 844],
  ['ru-hub-1440', 'https://fyzsxnb.com/ru/cars-from-china/', 1440, 900],
  ['ru-brand-1440', 'https://fyzsxnb.com/ru/cars-from-china/volkswagen/', 1440, 900],
  ['ru-model-1440', 'https://fyzsxnb.com/ru/cars-from-china/volkswagen/tayron/', 1440, 900],
  ['ru-archive-1440', 'https://fyzsxnb.com/category/russian-library/', 1440, 900],
  ['ru-archive-390', 'https://fyzsxnb.com/category/russian-library/', 390, 844],
  ['en-article-1440', 'https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts/', 1440, 900],
  ['ru-article-1440', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/', 1440, 900],
];

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const results = {};
  for (const [name, url, w, h] of TARGETS) {
    const page = await browser.newPage({ viewport: { width: w, height: h } });
    const errors = [];
    const fonts = []; // {url, size, external}
    page.on('pageerror', (e) => errors.push(String(e)));
    page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
    page.on('response', async (r) => {
      try {
        const ct = r.headers()['content-type'] || '';
        if (ct.includes('font') || r.url().includes('.woff2')) {
          const b = await r.body();
          fonts.push({ url: r.url(), size: b.length, external: !r.url().includes('fyzsxnb.com') });
        }
      } catch { /* ignore */ }
    });
    await page.addInitScript(() => {
      window.__fyzVitals = { cls: 0, lcp: 0 };
      try {
        new PerformanceObserver((list) => {
          for (const e of list.getEntries()) {
            if (e.hadRecentInput) continue;
            window.__fyzVitals.cls += e.value || 0;
          }
        }).observe({ type: 'layout-shift', buffered: true });
        new PerformanceObserver((list) => {
          const es = list.getEntries();
          if (es.length) window.__fyzVitals.lcp = es[es.length - 1].startTime;
        }).observe({ type: 'largest-contentful-paint', buffered: true });
      } catch { /* ignore */ }
    });
    try {
      await page.goto(url + (url.includes('?') ? '&' : '?') + 'x=033', { waitUntil: 'load', timeout: 60000 });
      await page.waitForTimeout(600);
      await page.screenshot({ path: path.join(outDir, stage, name + '.png'), fullPage: true });
      const metrics = await page.evaluate(() => {
        const h1 = document.querySelector('h1');
        const p = document.querySelector('.nv-content-wrap p, .fyz-deck, .cfc-deck, article p') || document.querySelector('p');
        const body = getComputedStyle(document.body);
        return {
          h1: document.querySelectorAll('h1').length,
          h1Font: h1 ? getComputedStyle(h1).fontFamily : null,
          h1Weight: h1 ? getComputedStyle(h1).fontWeight : null,
          bodyFont: body.fontFamily,
          overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
          vitals: window.__fyzVitals || { cls: 0, lcp: 0 },
          fontsReady: document.fonts ? document.fonts.status : 'n/a',
        };
      });
      const fontList = fonts.map((f) => ({ file: f.url.split('/').pop(), size: f.size, external: f.external }));
      results[name] = { ok: true, ...metrics, consoleErrors: errors.length, fonts: fontList, fontTotalKB: Math.round(fonts.reduce((a, f) => a + f.size, 0) / 1024) };
    } catch (e) {
      results[name] = { ok: false, error: String(e).slice(0, 160) };
    }
    await page.close();
  }
  await browser.close();
  console.log(JSON.stringify(results, null, 2));
  const bad = Object.values(results).filter((r) => !r.ok || r.overflow || r.h1 !== 1 || r.consoleErrors > 0);
  process.exit(bad.length ? 1 : 0);
})().catch((e) => { console.error(e); process.exit(2); });