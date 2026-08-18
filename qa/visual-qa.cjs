const fs = require('fs');
const path = require('path');
const { chromium } = require(path.join(process.env.FYZSXNB_NODE_MODULES, 'playwright'));

const root = path.resolve(__dirname, '..');
const preview = path.join(root, 'preview');
const cssPath = path.join(root, 'theme', 'fyzsxnb-neve-child', 'assets', 'css', 'research-wire.css');
const jsPath = path.join(root, 'theme', 'fyzsxnb-neve-child', 'assets', 'js', 'research-wire.js');
const outputDir = path.join(root, 'qa', 'screenshots');

fs.mkdirSync(outputDir, { recursive: true });

async function inspectHome(page, slug, viewport) {
  await page.setViewportSize(viewport);
  await page.goto(`http://127.0.0.1:8765/${slug}`, { waitUntil: 'domcontentloaded' });
  await page.addStyleTag({ path: cssPath });
  await page.screenshot({ path: path.join(outputDir, `${slug.replace('.html', '')}-${viewport.width}.png`), fullPage: true });

  return page.evaluate(() => {
    const root = document.documentElement;
    const signalGrid = document.querySelector('.fyz-signal-grid');
    const deskGrid = document.querySelector('.fyz-desk-grid');
    return {
      h1: document.querySelectorAll('h1').length,
      signals: document.querySelectorAll('.fyz-signal').length,
      desks: document.querySelectorAll('.fyz-desk').length,
      signalColumns: signalGrid ? getComputedStyle(signalGrid).gridTemplateColumns : null,
      deskColumns: deskGrid ? getComputedStyle(deskGrid).gridTemplateColumns : null,
      horizontalOverflow: root.scrollWidth > root.clientWidth + 1
    };
  });
}

async function inspectArticle(page) {
  const pageErrors = [];
  page.on('pageerror', (error) => pageErrors.push(String(error)));
  await page.setViewportSize({ width: 390, height: 844 });
  await page.goto('https://fyzsxnb.com/redmagic-cooler-6-pro-plus-china-launch-buyer-check/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.addStyleTag({ path: cssPath });
  await page.addScriptTag({ path: jsPath });
  await page.waitForTimeout(250);
  await page.screenshot({ path: path.join(outputDir, 'article-en-390.png'), fullPage: true });

  return page.evaluate((errors) => ({
    h1: document.querySelectorAll('h1').length,
    toc: document.querySelectorAll('.fyz-article-toc').length,
    tocLinks: document.querySelectorAll('.fyz-article-toc a').length,
    horizontalOverflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
    pageErrors: errors
  }), pageErrors);
}

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: process.env.FYZSXNB_CHROME || chromium.executablePath()
  });
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const result = {
    desktop: await inspectHome(page, 'home-en.html', { width: 1440, height: 900 }),
    mobile: await inspectHome(page, 'home-en.html', { width: 390, height: 844 }),
    russianMobile: await inspectHome(page, 'home-ru.html', { width: 390, height: 844 }),
    articleMobile: await inspectArticle(page)
  };
  await browser.close();
  console.log(JSON.stringify(result, null, 2));
})();
