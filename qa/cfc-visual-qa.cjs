// cfc-visual-qa.cjs — visual QA for the Cars from China model-page static preview.
// Serves preview/ over a local static server and screenshots the model preview at
// 1440 / 1024 / 768 / 390, checking single H1 + no horizontal overflow.
const fs = require('fs');
const http = require('http');
const path = require('path');
const { chromium } = require('C:/Users/Administrator/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright-core');

const root = path.resolve(__dirname, '..');
const preview = path.join(root, 'preview');
const outputDir = path.join(root, 'qa', 'screenshots', 'cfc');
const CHROME = 'C:/Users/Administrator/AppData/Local/ms-playwright/chromium-1223/chrome-win64/chrome.exe';
fs.mkdirSync(outputDir, { recursive: true });

const MIME = { '.html': 'text/html', '.css': 'text/css', '.js': 'text/javascript', '.png': 'image/png' };
const server = http.createServer((req, res) => {
  const urlPath = decodeURIComponent(new URL(req.url, 'http://x').pathname);
  let file = path.join(preview, urlPath === '/' ? 'cars-from-china-model-preview.html' : urlPath);
  if (!file.startsWith(preview)) { res.writeHead(403); return res.end(); }
  if (!fs.existsSync(file) || fs.statSync(file).isDirectory()) { res.writeHead(404); return res.end(); }
  res.writeHead(200, { 'Content-Type': MIME[path.extname(file)] || 'application/octet-stream' });
  res.end(fs.readFileSync(file));
});

async function shoot(page, viewport, label) {
  await page.setViewportSize(viewport);
  await page.goto(`http://127.0.0.1:8765/cars-from-china-model-preview.html`, { waitUntil: 'load' });
  await page.waitForTimeout(200);
  const name = `cfc-model-${label}-${viewport.width}.png`;
  await page.screenshot({ path: path.join(outputDir, name), fullPage: true });
  return page.evaluate(() => ({
    h1: document.querySelectorAll('h1').length,
    matrixRows: document.querySelectorAll('.cfc-matrix__row').length,
    cfcSections: [...document.querySelectorAll('.cfc-model-section, .cfc-section')].map((s) => s.querySelector('h2')?.textContent || '?'),
    cards: document.querySelectorAll('.cfc-card').length,
    overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1
  }));
}

(async () => {
  await new Promise((r) => server.listen(8765, r));
  const browser = await chromium.launch({ headless: true, executablePath: CHROME });
  const page = await browser.newPage();
  const result = {};
  for (const [label, vp] of [['desktop', { width: 1440, height: 900 }], ['tablet_1024', { width: 1024, height: 768 }], ['tablet_768', { width: 768, height: 1024 }], ['mobile_390', { width: 390, height: 844 }]]) {
    result[label] = await shoot(page, vp, label);
  }
  await browser.close();
  server.close();
  console.log(JSON.stringify(result, null, 2));
})().catch((e) => { console.error(e); process.exit(1); });