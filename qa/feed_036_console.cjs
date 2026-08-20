// feed_036_console.cjs — 0.3.6 console-error / failed-request / overflow sanity
// for the EN + RU homepages (and one article) with JS on.
const path = require('path');
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');
const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';

const TARGETS = [
  ['home-en-1440', 'https://fyzsxnb.com/', 1440, 900],
  ['home-en-390', 'https://fyzsxnb.com/', 390, 844],
  ['home-ru-1440', 'https://fyzsxnb.com/ru/', 1440, 900],
  ['home-ru-390', 'https://fyzsxnb.com/ru/', 390, 844],
  ['article-en-1440', 'https://fyzsxnb.com/fda-foreign-drug-establishment-registration-guide/', 1440, 900],
];

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const out = {};
  let allOk = true;
  for (const [name, url, w, h] of TARGETS) {
    const page = await browser.newPage({ viewport: { width: w, height: h } });
    const consoleErrors = [];
    const pageErrors = [];
    const failed = [];
    const asset404 = [];
    page.on('pageerror', (e) => pageErrors.push(String(e).slice(0, 300)));
    page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text().slice(0, 300)); });
    page.on('response', (r) => {
      if (r.status() >= 400) failed.push([r.status(), r.url().split('/').pop()]);
      const ct = r.headers()['content-type'] || '';
      if (r.status() === 404 && (ct.includes('font') || ct.includes('javascript') || ct.includes('stylesheet'))) {
        asset404.push(r.url());
      }
    });
    try {
      await page.goto(url, { waitUntil: 'load', timeout: 60000 });
      await page.waitForTimeout(2500); // let deferred JS run post-interaction-ish window
    } catch (e) {
      failed.push(['goto', String(e).slice(0, 200)]);
    }
    const overflow = await page.evaluate(() => {
      const doc = document.documentElement;
      return { scrollWidth: doc.scrollWidth, clientWidth: doc.clientWidth, overflow: doc.scrollWidth > doc.clientWidth + 1 };
    });
    out[name] = { consoleErrors, pageErrors, failed, asset404, overflow };
    if (consoleErrors.length || pageErrors.length || failed.length || asset404.length || overflow.overflow) {
      allOk = false;
    }
    await page.close();
  }
  await browser.close();
  const fs = require('fs');
  fs.writeFileSync(path.join(__dirname, 'feed_036_console_report.json'), JSON.stringify(out, null, 2));
  console.log(JSON.stringify({ allOk, summary: Object.fromEntries(Object.entries(out).map(([k, v]) => [k, {
    consoleErrors: v.consoleErrors.length, pageErrors: v.pageErrors.length,
    failed: v.failed, asset404: v.asset404.length, overflow: v.overflow,
  }])) }, null, 2));
  process.exit(allOk ? 0 : 2);
})();
