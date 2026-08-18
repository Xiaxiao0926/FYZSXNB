// ui2-032-shots.cjs — before/after screenshots for UI V2 0.3.2 CSS Hygiene.
const fs = require('fs');
const path = require('path');
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');

const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
const outDir = process.argv[2] || path.join(__dirname, 'screenshots', 'ui2-032');
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
    page.on('pageerror', (e) => errors.push(String(e)));
    page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
    try {
      await page.goto(url + (url.includes('?') ? '&' : '?') + 'x=032', { waitUntil: 'load', timeout: 60000 });
      await page.waitForTimeout(400);
      await page.screenshot({ path: path.join(outDir, stage, name + '.png'), fullPage: true });
      const metrics = await page.evaluate(() => ({
        h1: document.querySelectorAll('h1').length,
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
        scrollW: document.documentElement.scrollWidth,
        clientW: document.documentElement.clientWidth,
      }));
      results[name] = { ok: true, ...metrics, consoleErrors: errors.length, consoleErrorSamples: errors.slice(0, 3) };
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