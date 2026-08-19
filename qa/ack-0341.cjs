// ack-0341.cjs — 0.3.4.1 acceptance: comments off, footer brand, RU date, archive locale isolation + pagination.
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');
const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
// force load of research-wire TOC is not required here; shell is server-rendered.

const ARTICLE_RE = [
  'your email address will not be published',
  'will not be published',
  'name</label>', 'email</label>', 'website</label>',
  'comment-form', 'comment-reply-title', 'Comments are closed',
  'Powered by WordPress', 'Neve |',
];
const RU_ARTICLE_URLS = [
  'https://fyzsxnb.com/volkswagen-tayron-330tsi-dkv-dpl-dth-zapchasti-kitay/',   // ru
  'https://fyzsxnb.com/volkswagen-tayron-330tsi-kitay-gpf-opyt-vladeltsev/',     // ru
];
const EN_ARTICLE_URLS = [
  'https://fyzsxnb.com/china-market-volkswagen-tayron-dq381-emergency-mode-owner-cases/', // en
];
// RU-only post slugs that MUST NOT appear in EN archives
const RU_SLUGS = ['volkswagen-tayron', 'dq381-avariynyy', 'byd-frigate-07', 'openpilot', 'kak-proverit-byd', 'gpf'];
// EN-only slugs that MUST NOT appear in RU archive (russian-library)
const EN_SLUGS = ['china-market-volkswagen-tayron', 'dq381-emergency-mode', 'redmagic'];

function scan(reList) {
  const found = [];
  for (const s of reList) {
    if (document.body.innerHTML.toLowerCase().includes(s.toLowerCase())) found.push(s);
  }
  const meta = (document.querySelector('.fyz-article-top__meta') || {}).textContent || '';
  const pub = /Опубликовано[:]*\s*([^\n]+)/.exec(meta);
  const upd = /Обновлено[:]*\s*([^\n]+)/.exec(meta);
  const footer = (document.querySelector('.site-footer') || { innerText: '' }).innerText;
  return {
    commentStrings: found,
    hasCommentArea: !!document.querySelector('.nv-comments-wrap') || !!document.querySelector('#comments'),
    metaText: meta.replace(/\s+/g, ' ').trim().slice(0, 160),
    published: pub ? pub[1].trim() : null,
    updated: upd ? upd[1].trim() : null,
    footerText: footer.replace(/\s+/g, ' ').trim().slice(0, 120),
    h1: document.querySelectorAll('h1').length,
    overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
  };
}

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const out = { articles: {}, archives: {} };
  async function article(name, url) {
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await page.goto(url + '?x=41', { waitUntil: 'load', timeout: 60000 });
    out.articles[name] = await page.evaluate(scan, ARTICLE_RE);
    await page.close();
  }
  async function archive(name, url, forbiddenSlugs, expectRuOrEn) {
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await page.goto(url + '?x=41', { waitUntil: 'load', timeout: 60000 });
    const b = await page.evaluate((forb) => {
      const hrefs = Array.prototype.slice.call(document.querySelectorAll('.blog-entry a[href], .nv-index-posts a[href], article a[href]')).map((a) => a.href);
      const leak = hrefs.filter((h) => forb.some((s) => h.includes('/' + s + '/')));
      return { leak: leak.slice(0, 10), h1: document.querySelectorAll('h1').length, postCount: document.querySelectorAll('.blog-entry').length, overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1 };
    }, forbiddenSlugs);
    out.archives[name] = b;
    await page.close();
  }

  await article('ru-TAY01', RU_ARTICLE_URLS[0]);
  await article('ru-TAY02', RU_ARTICLE_URLS[1]);
  await article('en-DQ381', EN_ARTICLE_URLS[0]);
  await archive('cat-china-tech-p1', 'https://fyzsxnb.com/category/china-tech-products/', RU_SLUGS, 'en');
  await archive('cat-china-tech-p2', 'https://fyzsxnb.com/category/china-tech-products/page/2/', RU_SLUGS, 'en');
  await archive('cat-product-research-p1', 'https://fyzsxnb.com/category/product-research/', RU_SLUGS, 'en');
  await archive('cat-russian-library-p1', 'https://fyzsxnb.com/category/russian-library/', EN_SLUGS, 'ru');

  console.log(JSON.stringify(out, null, 2));
  await browser.close();
  // verdict
  const artsOk = Object.entries(out.articles).every(([k, v]) => v.commentStrings.length === 0 && v.h1 === 1 && !v.overflow);
  const ruDateOk = /[а-яё]/.test(out.articles['ru-TAY01'].published || '') && !/January|February|March|April|May|June|July|August|September|October|November|December/.test(out.articles['ru-TAY01'].published || '');
  const archesOk = Object.entries(out.archives).every(([k, v]) => v.leak.length === 0 && v.h1 === 1 && !v.overflow);
  const footerOk = Object.entries(out.articles).every(([k, v]) => /\bPowered by WordPress\b/.test(v.footerText) === false);
  console.log(JSON.stringify({ verdict: { articlesNoComments: artsOk, ruDateOk, archivesIsolated: archesOk, footerNoPoweredBy: footerOk } }));
  process.exit(artsOk && ruDateOk && archesOk && footerOk ? 0 : 1);
})().catch((e) => { console.error(e); process.exit(2); });