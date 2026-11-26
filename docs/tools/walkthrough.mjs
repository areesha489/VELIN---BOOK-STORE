import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const shots = path.join(root, 'snapshots');
const videoDir = path.join(root, 'video');
const base = process.env.VELIN_URL || 'http://127.0.0.1:8000';

fs.mkdirSync(shots, { recursive: true });
fs.mkdirSync(videoDir, { recursive: true });

const pause = (ms = 900) => new Promise((r) => setTimeout(r, ms));

async function shot(page, name) {
  const file = path.join(shots, `${name}.png`);
  await page.screenshot({ path: file, fullPage: false });
  console.log('shot', name);
  return file;
}

async function login(page, email, password) {
  await page.goto(`${base}/login`, { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  await pause(700);
}

async function logout(page) {
  const profile = page.getByRole('link', { name: /Rohan|Meera|Aisha|Areesha|Reader|Editor/i }).first();
  if (await page.getByRole('link', { name: 'Sign in' }).count()) {
    return;
  }
  await page.goto(`${base}/account`, { waitUntil: 'networkidle' });
  const logoutBtn = page.locator('button[type="submit"]', { hasText: 'Sign out' }).first();
  if (await logoutBtn.count()) {
    await logoutBtn.click();
    await page.waitForLoadState('networkidle');
  }
  await pause(500);
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    recordVideo: { dir: videoDir, size: { width: 1440, height: 900 } },
  });
  const page = await context.newPage();

  // 1. Home
  await page.goto(base, { waitUntil: 'networkidle' });
  await pause(1200);
  await shot(page, '01-home');

  // Search Salt
  await page.fill('#nav-q', 'Salt');
  await page.keyboard.press('Enter');
  await page.waitForLoadState('networkidle');
  await pause(900);
  await shot(page, '02-search-salt');

  // Filter thriller + sort price
  await page.goto(`${base}/books?genre=thriller&sort=price_asc`, { waitUntil: 'networkidle' });
  await pause(900);
  await shot(page, '03-catalog-thriller');

  // Open a book
  await page.goto(`${base}/books/the-last-signal`, { waitUntil: 'networkidle' });
  await pause(1000);
  await shot(page, '04-book-detail');

  // Guest cart add
  await page.click('button:has-text("Add to cart")');
  await page.waitForLoadState('networkidle');
  await pause(800);

  // Login as reader
  await login(page, 'reader@velin.store', 'Reader#2026');
  await shot(page, '05-reader-home');

  // Save wishlist from book
  await page.goto(`${base}/books/the-salt-atlas`, { waitUntil: 'networkidle' });
  await pause(700);
  const saveBtn = page.locator('button', { hasText: /Save to wishlist|Saved|Save/ }).first();
  if (await saveBtn.count()) {
    await saveBtn.click();
    await page.waitForLoadState('networkidle');
  }
  await pause(600);

  // Cart
  await page.goto(`${base}/cart`, { waitUntil: 'networkidle' });
  await pause(900);
  await shot(page, '06-cart');

  // Checkout if cart has items, else add then checkout
  if (await page.locator('text=Nothing in the bag').count()) {
    await page.goto(`${base}/books/the-last-signal`, { waitUntil: 'networkidle' });
    await page.click('button:has-text("Add to cart")');
    await page.waitForLoadState('networkidle');
    await page.goto(`${base}/cart`, { waitUntil: 'networkidle' });
  }

  await page.click('a:has-text("Checkout")');
  await page.waitForLoadState('networkidle');
  await pause(900);
  await shot(page, '07-checkout');

  // Place order if possible
  const place = page.locator('button:has-text("Place order"), button:has-text("Place the order"), button[type="submit"]').last();
  if (await place.count()) {
    await place.click();
    await page.waitForLoadState('networkidle');
    await pause(1200);
    await shot(page, '08-order-placed');
  }

  // Orders list / show
  await page.goto(`${base}/account/orders`, { waitUntil: 'networkidle' });
  await pause(900);
  await shot(page, '09-orders');
  const firstOrder = page.locator('a.order-row, a[href*="/account/orders/"]').first();
  if (await firstOrder.count()) {
    await firstOrder.click();
    await page.waitForLoadState('networkidle');
    await pause(1000);
    await shot(page, '10-order-timeline');
  }

  // Review on Salt Atlas
  await page.goto(`${base}/books/the-salt-atlas#reviews`, { waitUntil: 'networkidle' });
  await pause(800);
  await shot(page, '11-reviews');

  // Wishlist
  await page.goto(`${base}/wishlist`, { waitUntil: 'networkidle' });
  await pause(800);
  await shot(page, '12-wishlist');

  // Logout and admin
  await logout(page);
  await login(page, 'admin@velin.store', 'VelinAdmin#2026');
  await page.goto(`${base}/admin`, { waitUntil: 'networkidle' });
  await pause(1100);
  await shot(page, '13-admin-overview');

  await page.goto(`${base}/admin/books`, { waitUntil: 'networkidle' });
  await pause(900);
  await shot(page, '14-admin-books');

  await page.goto(`${base}/admin/orders`, { waitUntil: 'networkidle' });
  await pause(900);
  await shot(page, '15-admin-orders');

  const adminOrder = page.locator('table a').first();
  if (await adminOrder.count()) {
    await adminOrder.click();
    await page.waitForLoadState('networkidle');
    await pause(900);
    await shot(page, '16-admin-order-detail');

    const status = page.locator('select[name="status"]');
    if (await status.count()) {
      await status.selectOption('shipped');
      const tracking = page.locator('input[name="tracking_code"], input[name="tracking"]');
      if (await tracking.count()) {
        await tracking.fill('VELIN-TRACK-2026');
      }
      const carrier = page.locator('input[name="carrier"]');
      if (await carrier.count()) {
        await carrier.fill('BlueDart');
      }
      const save = page.locator('button[type="submit"]').first();
      if (await save.count()) {
        await save.click();
        await page.waitForLoadState('networkidle');
        await pause(900);
        await shot(page, '17-admin-shipped');
      }
    }
  }

  // Reader tracking
  await logout(page);
  await login(page, 'reader@velin.store', 'Reader#2026');
  await page.goto(`${base}/account/orders`, { waitUntil: 'networkidle' });
  await pause(700);
  const readerOrder = page.locator('a.order-row, a[href*="/account/orders/"]').first();
  if (await readerOrder.count()) {
    await readerOrder.click();
    await page.waitForLoadState('networkidle');
    await pause(1100);
    await shot(page, '18-reader-tracking');
  }

  await pause(800);
  await context.close();
  await browser.close();

  const videos = fs.readdirSync(videoDir).filter((f) => f.endsWith('.webm'));
  if (videos.length) {
    const latest = videos
      .map((f) => ({ f, t: fs.statSync(path.join(videoDir, f)).mtimeMs }))
      .sort((a, b) => b.t - a.t)[0].f;
    const dest = path.join(videoDir, 'velin-walkthrough.webm');
    fs.copyFileSync(path.join(videoDir, latest), dest);
    console.log('video', dest);
  }

  console.log('done');
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
