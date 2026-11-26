import {
  Document,
  Packer,
  Paragraph,
  TextRun,
  HeadingLevel,
  ImageRun,
  PageBreak,
  Table,
  TableRow,
  TableCell,
  WidthType,
  BorderStyle,
  AlignmentType,
  ShadingType,
  Header,
  Footer,
  PageNumber,
} from 'docx';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const shots = path.join(root, 'snapshots');
const outName = process.env.VELIN_DOCX || 'VELIN-Project-Presentation.docx';
const out = path.join(root, outName);

const ink = '16140F';
const muted = '555555';
const accent = 'C2410C';
const lime = '3F6212';
const paper = 'F7F3EA';

function titleSlide(lines) {
  return lines.map(
    (line, i) =>
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: i === 0 ? 900 : 100, after: 70 },
        children: [
          new TextRun({
            text: line.text,
            bold: !!line.bold,
            size: line.size || 28,
            color: line.color || ink,
            font: 'Calibri',
          }),
        ],
      }),
  );
}

function h1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 320, after: 140 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 12, color: accent, space: 8 } },
    children: [new TextRun({ text, bold: true, size: 32, color: ink, font: 'Calibri' })],
  });
}

function h2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    spacing: { before: 240, after: 110 },
    children: [new TextRun({ text, bold: true, size: 26, color: accent, font: 'Calibri' })],
  });
}

function h3(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_3,
    spacing: { before: 160, after: 80 },
    children: [new TextRun({ text, bold: true, size: 24, color: lime, font: 'Calibri' })],
  });
}

function p(text) {
  return new Paragraph({
    spacing: { after: 130 },
    children: [new TextRun({ text, size: 22, color: ink, font: 'Calibri' })],
  });
}

function lead(text) {
  return new Paragraph({
    spacing: { after: 170 },
    children: [new TextRun({ text, size: 24, italics: true, color: muted, font: 'Calibri' })],
  });
}

function bullet(text) {
  return new Paragraph({
    spacing: { after: 85 },
    bullet: { level: 0 },
    children: [new TextRun({ text, size: 22, color: ink, font: 'Calibri' })],
  });
}

function caption(text) {
  return new Paragraph({
    spacing: { before: 50, after: 200 },
    alignment: AlignmentType.CENTER,
    children: [new TextRun({ text, italics: true, size: 18, color: muted, font: 'Calibri' })],
  });
}

function pageBreak() {
  return new Paragraph({ children: [new PageBreak()] });
}

function imageBlock(file, label) {
  const full = path.join(shots, file);
  if (!fs.existsSync(full)) {
    return [p(`[Snapshot missing: ${file}]`), caption(label)];
  }
  const data = fs.readFileSync(full);
  return [
    new Paragraph({
      alignment: AlignmentType.CENTER,
      spacing: { before: 120, after: 40 },
      children: [
        new ImageRun({
          type: 'png',
          data,
          transformation: { width: 600, height: 375 },
          altText: { title: label, description: label, name: file },
        }),
      ],
    }),
    caption(label),
  ];
}

function cell(text, opts = {}) {
  const { bold = false, fill = null, width = 3000 } = opts;
  return new TableCell({
    width: { size: width, type: WidthType.DXA },
    shading: fill ? { type: ShadingType.CLEAR, fill } : undefined,
    borders: {
      top: { style: BorderStyle.SINGLE, size: 4, color: 'D6D0C4' },
      bottom: { style: BorderStyle.SINGLE, size: 4, color: 'D6D0C4' },
      left: { style: BorderStyle.SINGLE, size: 4, color: 'D6D0C4' },
      right: { style: BorderStyle.SINGLE, size: 4, color: 'D6D0C4' },
    },
    children: [
      new Paragraph({
        spacing: { before: 55, after: 55 },
        children: [new TextRun({ text, bold, size: 20, color: ink, font: 'Calibri' })],
      }),
    ],
  });
}

function table(headers, rows) {
  const col = Math.floor(9000 / headers.length);
  return new Table({
    width: { size: 9000, type: WidthType.DXA },
    rows: [
      new TableRow({
        children: headers.map((h) => cell(h, { bold: true, fill: paper, width: col })),
      }),
      ...rows.map(
        (row) =>
          new TableRow({
            children: row.map((value) => cell(String(value), { width: col })),
          }),
      ),
    ],
  });
}

const children = [
  // COVER
  ...titleSlide([
    { text: 'VELIN', bold: true, size: 76, color: accent },
    { text: 'After-Dark Bookstore', size: 30, color: muted },
    { text: 'Project Presentation', bold: true, size: 34 },
    { text: 'Aptech eProject', size: 24, color: muted },
    { text: 'Reading room · Kala Ghoda, Mumbai', size: 22, color: lime },
  ]),
  new Paragraph({ spacing: { before: 500 }, children: [] }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 80 },
    children: [new TextRun({ text: 'Presented by', size: 20, color: muted, font: 'Calibri' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 40 },
    children: [new TextRun({ text: 'AREESHA SHAHID', bold: true, size: 26, color: ink, font: 'Calibri' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 40 },
    children: [new TextRun({ text: 'HUSSAIN ZIA', bold: true, size: 26, color: ink, font: 'Calibri' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 200 },
    children: [new TextRun({ text: 'KASSIM EHSAAN', bold: true, size: 26, color: ink, font: 'Calibri' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    children: [new TextRun({ text: `Document date: ${new Date().toISOString().slice(0, 10)}`, size: 18, color: muted })],
  }),
  pageBreak(),

  // AGENDA
  h1('1. Agenda'),
  bullet('About VELIN and the place behind the brand'),
  bullet('Team'),
  bullet('What makes this project unique'),
  bullet('Complete feature list'),
  bullet('Login accounts for demo'),
  bullet('Store experience (with screenshots)'),
  bullet('Reader account panels'),
  bullet('Editor desk panels'),
  bullet('Business rules (price, dispatch, tracking)'),
  bullet('Walkthrough video'),
  pageBreak(),

  // ABOUT + PLACE
  h1('2. About the Project'),
  lead('VELIN is an online night bookstore — a digital reading room for people who buy books after the office lights go out.'),
  p('It is built as a complete e-commerce bookstore for the Aptech eProject. A visitor can browse without an account. Buying, wishlist, reviews, and delivery tracking need a sign-in. An editor desk manages books, readers, and orders.'),
  h2('Place & brand setting'),
  p('The shop is set in Mumbai’s art district. The brand voice and footer treat VELIN as a physical reading room that also ships parcels across India.'),
  table(
    ['Detail', 'Information'],
    [
      ['Brand', 'VELIN — after-dark bookselling'],
      ['Place', 'Kala Ghoda, Mumbai'],
      ['Address', '18 Ropewalk Lane, Kala Ghoda, Mumbai 400001'],
      ['Desk hours', '10:00–19:00 IST'],
      ['Currency', 'INR (₹) — prices include GST'],
      ['Dispatch', '₹49 · free when cart total reaches ₹999'],
      ['Live demo URL', 'http://127.0.0.1:8000'],
    ],
  ),
  ...imageBlock('01-home.png', 'VELIN home — night bookstore hero in the Mumbai reading-room voice'),
  pageBreak(),

  // TEAM
  h1('3. Project Team'),
  lead('This eProject is presented by the following team members.'),
  table(
    ['#', 'Team Member', 'Role on presentation'],
    [
      ['1', 'AREESHA SHAHID', 'Team member'],
      ['2', 'HUSSAIN ZIA', 'Team member'],
      ['3', 'KASSIM EHSAAN', 'Team member'],
    ],
  ),
  p('Together the team delivered a working bookstore: storefront, cart and checkout, account area, delivery tracking, and an editor desk — with documentation, screenshots, and a demo video.'),
  pageBreak(),

  // UNIQUE
  h1('4. What Makes VELIN Unique'),
  lead('Not a generic template shop — a classroom project with a clear place, original catalog, and end-to-end order story.'),
  bullet('Place-first brand: Kala Ghoda reading room, Ropewalk Lane address, Mumbai desk hours — not a faceless cart.'),
  bullet('Original catalog: titles, authors, and covers are created for the project (no borrowed publisher art).'),
  bullet('Night bookstore look: dark storefront for readers + light editor desk for staff — two clear worlds.'),
  bullet('Full journey in one app: browse → save → cart → checkout → timeline → tracking code.'),
  bullet('Guest cart that merges when you sign in — shop first, account later.'),
  bullet('Safe rehearsal payments: card 4242… is checked, then only brand + last four digits are kept.'),
  bullet('Editor desk that can ship with carrier + tracking, and the reader sees that code on the order.'),
  bullet('Wishlist, reviews, and likes — social reading features beyond a plain catalog.'),
  bullet('Classroom social login demos (Google / GitHub) for local marking without live OAuth keys.'),
  pageBreak(),

  // FEATURES
  h1('5. Complete Feature List'),
  h2('For every visitor'),
  bullet('Home with bestsellers, new arrivals, and genre shelves'),
  bullet('Catalog with search (title, author, genre, ISBN)'),
  bullet('Filters and sort (price, popularity, release date)'),
  bullet('Bestsellers and New arrivals collections'),
  bullet('Book detail: cover, price, stock, description, author'),
  bullet('Add to cart with quantity'),
  bullet('Guest browsing and guest cart'),
  h2('For signed-in readers'),
  bullet('Register, sign in, sign out'),
  bullet('Forgot password / reset flow'),
  bullet('Profile (name, email, phone, password)'),
  bullet('Shipping addresses'),
  bullet('Payment methods (card / UPI)'),
  bullet('Wishlist (Save / List)'),
  bullet('Checkout with address + payment'),
  bullet('Order history and delivery timeline'),
  bullet('Tracking code when the parcel is shipped'),
  bullet('Ratings, reviews, and likes on books'),
  h2('For editors (admin desk)'),
  bullet('Overview dashboard (titles, open orders, readers, revenue)'),
  bullet('Manage books, genres, and authors'),
  bullet('Manage orders and status'),
  bullet('Add carrier and tracking code'),
  bullet('Manage readers (role and active status)'),
  bullet('Low-stock alerts on the overview'),
  pageBreak(),

  // LOGIN
  h1('6. Login Accounts (Demo)'),
  lead('Use these classroom accounts to present the reader flow and the editor desk.'),
  table(
    ['Who', 'Email', 'Password', 'What you can show'],
    [
      ['Reader', 'reader@velin.store', 'Reader#2026', 'Cart, checkout, orders, wishlist, reviews'],
      ['Reader', 'meera@velin.store', 'Meera#2026', 'Second reader / reviews'],
      ['Editor', 'admin@velin.store', 'VelinAdmin#2026', 'Desk: books, orders, tracking, readers'],
    ],
  ),
  h2('How to sign in'),
  bullet('Open http://127.0.0.1:8000/login'),
  bullet('Enter the email and password exactly (including # in the password)'),
  bullet('After editor login, open Desk in the header or go to /admin'),
  bullet('Local demo also shows Continue with Google / GitHub classroom profiles'),
  h2('Test payment card'),
  p('At checkout use card number 4242 4242 4242 4242 with any future expiry. VELIN checks the number, then stores only the card brand and last four digits — nothing is sent to a bank.'),
  pageBreak(),

  // STORE JOURNEY
  h1('7. Store Experience'),
  h2('7.1 Home — enter the stacks'),
  p('The home page introduces VELIN, shelf counts, free dispatch from ₹999, bestsellers, new arrivals, and genres.'),
  ...imageBlock('01-home.png', 'Home page'),

  h2('7.2 Search & catalog'),
  p('Search finds titles quickly. Genre filters and sort options narrow the shelf for the presentation.'),
  ...imageBlock('02-search-salt.png', 'Search — “Salt”'),
  ...imageBlock('03-catalog-thriller.png', 'Catalog — Thriller, sorted by price'),

  h2('7.3 Book page'),
  p('Each book shows price, stock, details, Add to cart, and Save to wishlist. Reviews sit on the same page.'),
  ...imageBlock('04-book-detail.png', 'Book detail'),

  h2('7.4 Cart & checkout'),
  p('The cart shows line totals and dispatch. Checkout asks for a real address and a payment method, then places the order.'),
  ...imageBlock('06-cart.png', 'Shopping cart'),
  ...imageBlock('07-checkout.png', 'Checkout'),
  pageBreak(),

  // ACCOUNT
  h1('8. Reader Account Panels'),
  p('Signed-in readers use Profile, Addresses, Payments, Orders, and Wishlist from the account menu.'),
  ...imageBlock('05-reader-home.png', 'Signed in as a reader'),
  ...imageBlock('09-orders.png', 'Order history'),
  ...imageBlock('10-order-timeline.png', 'Order timeline'),
  ...imageBlock('08-order-placed.png', 'Order after checkout'),
  ...imageBlock('11-reviews.png', 'Reviews panel'),
  ...imageBlock('12-wishlist.png', 'Wishlist'),
  ...imageBlock('18-reader-tracking.png', 'Tracking code on the reader’s order'),
  pageBreak(),

  // ADMIN
  h1('9. Editor Desk Panels'),
  lead('Editors sign in with admin@velin.store and open Desk. This is the staff side of VELIN.'),
  h2('Overview'),
  p('Tonight’s room shows live counts: titles, open orders, readers, and revenue — plus recent orders and low stock.'),
  ...imageBlock('13-admin-overview.png', 'Editor overview'),
  h2('Books & orders'),
  p('Books panel manages the catalog. Orders panel updates status, carrier, and tracking so the reader can follow the parcel.'),
  ...imageBlock('14-admin-books.png', 'Books panel'),
  ...imageBlock('15-admin-orders.png', 'Orders list'),
  ...imageBlock('16-admin-order-detail.png', 'Order detail on the desk'),
  ...imageBlock('17-admin-shipped.png', 'Order marked Shipped with tracking'),
  h2('Also on the desk'),
  bullet('Genres — sections of the catalog'),
  bullet('Authors — writers linked to titles'),
  bullet('Readers — activate/pause accounts or change role'),
  pageBreak(),

  // BUSINESS RULES
  h1('10. Business Rules in the Project'),
  table(
    ['Rule', 'How VELIN handles it'],
    [
      ['Currency', 'All prices in INR with GST included'],
      ['Dispatch', '₹49 per order'],
      ['Free dispatch', 'When book subtotal reaches ₹999'],
      ['Stock', 'Checkout refuses the order if copies ran out'],
      ['Order history', 'Address and payment are frozen on the order'],
      ['Tracking', 'Required when status becomes Shipped / Out for delivery / Delivered'],
      ['Cancel', 'Cancelled orders return copies to the shelf'],
      ['Reviews', 'One review per reader per book; likes on others’ notes'],
    ],
  ),
  pageBreak(),

  // VIDEO
  h1('11. Project Walkthrough Video'),
  p('A full working demo is saved with the project documents:'),
  bullet('docs/video/velin-walkthrough.webm'),
  p('The video shows: home → search & filter → book → cart → reader login → checkout → orders → reviews → wishlist → editor desk → ship with tracking → reader sees tracking.'),
  pageBreak(),

  // CLOSE
  h1('12. Closing'),
  p('VELIN is a complete night bookstore project set in Kala Ghoda, Mumbai. It covers discovery, purchase, account care, delivery tracking, and staff operations — with a unique place-led brand, original catalog, and a clear reader-to-editor story.'),
  p('Team: AREESHA SHAHID · HUSSAIN ZIA · KASSIM EHSAAN'),
  p('Place: 18 Ropewalk Lane, Kala Ghoda, Mumbai 400001'),
  p('Demo login: reader@velin.store / Reader#2026 · admin@velin.store / VelinAdmin#2026'),
  new Paragraph({
    spacing: { before: 360 },
    alignment: AlignmentType.CENTER,
    children: [new TextRun({ text: 'Thank you', bold: true, size: 36, color: accent, font: 'Calibri' })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { before: 120 },
    children: [new TextRun({ text: 'VELIN · After-dark bookselling · Aptech eProject', italics: true, size: 20, color: muted })],
  }),
];

const doc = new Document({
  creator: 'VELIN Project Team',
  title: 'VELIN — Project Presentation',
  description: 'Project presentation for VELIN bookstore: team, place, features, login, panels, screenshots.',
  sections: [
    {
      properties: {
        page: { margin: { top: 720, bottom: 720, left: 720, right: 720 } },
      },
      headers: {
        default: new Header({
          children: [
            new Paragraph({
              alignment: AlignmentType.RIGHT,
              children: [
                new TextRun({
                  text: 'VELIN  ·  Project Presentation  ·  Areesha Shahid · Hussain Zia · Kassim Ehsaan',
                  size: 15,
                  color: muted,
                  font: 'Calibri',
                }),
              ],
            }),
          ],
        }),
      },
      footers: {
        default: new Footer({
          children: [
            new Paragraph({
              alignment: AlignmentType.CENTER,
              children: [
                new TextRun({ text: 'Page ', size: 16, color: muted }),
                new TextRun({ children: [PageNumber.CURRENT], size: 16, color: muted }),
                new TextRun({ text: '  ·  Kala Ghoda, Mumbai', size: 16, color: muted }),
              ],
            }),
          ],
        }),
      },
      children,
    },
  ],
});

const buffer = await Packer.toBuffer(doc);
fs.writeFileSync(out, buffer);
console.log('wrote', out, buffer.length, 'bytes');

// Also try classic filename if unlocked
const classic = path.join(root, 'VELIN-Developer-Documentation.docx');
try {
  fs.writeFileSync(classic, buffer);
  console.log('also wrote', classic);
} catch (err) {
  console.log('classic file locked, skipped:', err.code || err.message);
}
