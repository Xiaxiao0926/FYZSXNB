// toc-acceptance.cjs — TOC 专项验收 (0.3.4): EN/RU/390, 4 article types, anchors, dup, back, JS-off.
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');
const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';

const CASES = [
  ['en-long', 'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', 1440],
  ['en-long-390', 'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', 390],
  ['ru-longtitle', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/', 1440],
  ['en-table', 'https://fyzsxnb.com/china-market-volkswagen-tayron-330tsi-dkv-dpl-dth-parts/', 1440],
  ['ru-article2', 'https://fyzsxnb.com/volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev/', 1440],
];

async function probe(browser, url, w, jsoff) {
  const page = await browser.newPage({ viewport: { width: w, height: 900 } });
  const errors = [];
  page.on('pageerror', (e) => errors.push(String(e)));
  if (jsoff) {
    await page.addInitScript(() => { window.__fyzTocLoaded = true; });
  }
  await page.goto(url + '?x=tocA', { waitUntil: 'load', timeout: 60000 });
  // forceful interaction to release LiteSpeed deferred JS
  await page.mouse.move(200, 200).catch(() => {});
  await page.mouse.wheel(0, 900).catch(() => {});
  await page.evaluate(() => window.scrollTo(0, 240)).catch(() => {});
  await page.mouse.down().catch(() => {});
  await page.mouse.up().catch(() => {});
  let toc;
  try {
    await page.waitForSelector('.fyz-article-toc', { timeout: 6000 });
    toc = true;
  } catch { toc = false; }
  await page.waitForTimeout(400);
  const m = await page.evaluate(() => {
    const t = document.querySelector('.fyz-article-toc');
    const wrap = document.querySelector('.nv-single-post-wrap.col');
    const ids = Array.prototype.slice.call(document.querySelectorAll('[id]')).map((e) => e.id);
    const dups = ids.filter((id, i) => ids.indexOf(id) !== i);
    // anchor target validity
    let anchorBroken = 0, anchorTotal = 0;
    if (t) {
      t.querySelectorAll('a[href^="#fyz-"]').forEach((a) => {
        anchorTotal += 1;
        if (!document.getElementById(a.getAttribute('href').slice(1))) anchorBroken += 1;
      });
    }
    return {
      toc: !!t,
      tocLinks: t ? t.querySelectorAll('a[href^="#fyz-"]').length : 0,
      summary: t ? ((t.querySelector('summary') || {}).textContent || null) : null,
      aria: t ? (t.getAttribute('aria-label') || null) : null,
      back: t ? ((t.querySelector('.fyz-toc-back') || {}).textContent || null) : null,
      hasHasToc: wrap ? wrap.classList.contains('fyz-has-toc') : false,
      gridCols: wrap ? (t ? getComputedStyle(wrap).gridTemplateColumns : 'none') : null,
      dupIds: dups.length,
      anchorTotal, anchorBroken,
      overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
    };
  });
  await page.close();
  return { ...m, consoleErrors: errors.length };
}

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const out = {};
  for (const [name, url, w] of CASES) {
    out[name] = await probe(browser, url, w, false);
  }
  out['en-long-js-off'] = await probe(browser, 'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', 1440, true);
  console.log(JSON.stringify(out, null, 2));
  await browser.close();
  const fails = Object.entries(out).filter(([k, v]) => !(v.overflow === false && v.consoleErrors === 0 && v.dupIds === 0 && v.anchorBroken === 0 && (k.includes('off') ? v.toc === false : v.toc === true)));
  process.exit(fails.length ? 1 : 0);
})().catch((e) => { console.error(e); process.exit(2); });