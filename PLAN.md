# Elkris Foods Website — Implementation Plan

> Source of truth for rebuilding the Elkris Foods website.
> Derived from `ELKRIS FOODS WEBSITE AUDIT.pdf`. Delivery scope follows the audit's four-phase priority ranking.
> Decisions locked: **WhatsApp-first commerce** (no payment gateway in scope) · **Filament 5 admin** · **full 4-phase roadmap**.

---

## 1. Brand Foundation

**Positioning:** ELKRIS FOODS — *Better Choices. Better Meals. Better Living.*

**Official brand messages (use verbatim in designated surfaces):**

- **Primary tagline:** "Trusted for Healthy Foods"
  → Hero headline: `ELKRIS… TRUSTED FOR HEALTHY FOODS`
- **Secondary positioning line:** "Blood Sugar Friendly Alternatives"
  → Hero subtext, product badges, meta/OG copy, brand story

**Usage guardrail (audit §17):** The two taglines are client-approved brand copy used verbatim in marketing surfaces (hero, taglines, OG/meta, packaging-consistent spots). Product-page and article body copy still follows qualified-claims rules:

- ✅ "A fibre-rich option for people making more conscious food choices."
- ✅ "Contains beta-glucan, a soluble fibre associated with heart-health benefits as part of a balanced diet."
- ❌ "Controls diabetes" / "Lowers blood sugar" / any treatment claims

Tagline ≠ medical claim; keep them in their designated layers.

**Success test:** A first-time visitor answers five questions within 10–20 seconds:

1. What is Elkris Foods?
2. What products do you sell?
3. Why should I choose them?
4. Where can I buy them?
5. How can I order right now?

---

## 2. Current State

Pristine Laravel React Starter Kit: Laravel 13, Inertia v2, React 19, Tailwind v4, Fortify auth, shadcn-style UI kit, Wayfinder. **Zero Elkris application code exists.** Brand assets (logo, product photos, 6 videos) sit unorganized in `public/`. Everything is built from scratch; only content/data quality must be gotten right from day one.

**Add to stack:** Filament 5 (`composer require filament/filament:"~5.0"` — use `~` on PowerShell) for `/admin`. Compatible: Laravel 11.28+ required, we have 13.x.

---

## 3. Information Architecture & Navigation

```
Header:  HOME | SHOP | PRODUCTS | RECIPES | HEALTH & TIPS | WHERE TO BUY | ABOUT | CONTACT   [SHOP NOW]
Mobile:  hamburger nav + persistent bottom bar: Home | Shop | Stores | WhatsApp | Account
Global:  Floating WhatsApp button (every page) · site-wide search
Footer:  nav columns · contact block (real Elkris details only) · FOLLOW ELKRIS socials
         · newsletter signup · FAQ links
```

### Public routes (Inertia)

| Route | Page | Audit ref |
|---|---|---|
| `/` | Homepage (13-section structure, §5) | #32 |
| `/products`, `/products/{slug}` | Catalog + dedicated product pages | #4 |
| `/shop` | Shop (search/filter/sort) | #7 |
| `/recipes`, `/recipes/{slug}` | Recipe Hub | #6 |
| `/health`, `/health/{slug}` | Health & Food education hub | #18 |
| `/where-to-buy` | Store locator (map + list) | #9 |
| `/about` | Rewritten About + brand story | #14–16 |
| `/contact` | Contact + guided intent selector | #11–12 |
| `/distributor` | Distributor/wholesale application | #13 |
| `/gallery` | Media hub (categorized, clickable) | #20 |
| `/search?q=` | Site-wide search | #27 |
| `/orders/{number}` | WhatsApp order status lookup | Phase 2 |
| `/account/*` | Customer account (reuse Fortify auth) | #22 |
| `/admin` | Filament panel | — |

---

## 4. Homepage Structure (audit §32)

1. **Hero** — full-bleed meal + pack image/video; headline `ELKRIS… TRUSTED FOR HEALTHY FOODS`; subtext with "Blood Sugar Friendly Alternatives" + versatility line; CTAs **SHOP NOW** + **FIND A STORE / EXPLORE OUR PRODUCTS**
2. **Trust bar** — Blood Sugar Friendly · Versatile · Nutritious · Made for Everyday Meals
3. **Quick product categories** — 4 clickable cards (Oat & Fibre Flour / Oat Flour / Instant Plantain / Breakfast & Cereals)
4. **Discover Elkris** — One brand, many ways: Swallow | Breakfast | Baking | Porridge
5. **Our Products** — product cards (image, name, sizes, price, Shop / WhatsApp CTAs)
6. **What can you make?** — interactive meal selector (§6.1)
7. **Why Elkris?** — benefits with qualified health-claim language (§7)
8. **Recipes** — 3–6 cards → Recipe Hub
9. **Watch & Learn** — video modules (How to prepare, 5 ways to use, customer stories)
10. **Customer Stories** — testimonials / video reviews
11. **Find Elkris Near You** — embedded mini store locator
12. **Shop Online + Join the Community** — featured products + newsletter/WhatsApp/social
13. **Footer**

---

## 5. Data Model (Filament-managed)

```
settings (single)   primary_tagline, positioning_tagline, contact phone/email,
                    address, social URLs, WhatsApp number, GA4 ID, Meta Pixel ID,
                    announcement banner — NO hardcoded copy anywhere
products            slug, name, tagline, description, benefits (json),
                    nutrition_notes, hero_image, gallery (json), is_published
product_variants    product_id, size_label, pack_type (single|carton), price, sku
categories          name, slug, image (Oat-Based / Plantain / Breakfast & Cereals)
meal_types          name, slug, sort (Swallow / Breakfast / Baking / Porridge)
product_meal_type   pivot + best_with soups (Egusi, Okro, Ogbono, Afang, Efo Riro…)
recipes             title, slug, image, category, prep_minutes, difficulty,
                    product_id (shop CTA), ingredients (json), steps (json), published
articles            Health Hub: title, slug, body, cover, category, meta
stores              name, address, city, state, phone, hours, lat/lng,
                    products_available, google_maps_url  ← cleaned retailer DB
gallery_items       media, category (Products|Recipes|Customers|Events|
                    Activations|CSR|Team|Media), caption
testimonials        name, location, quote, photo, video_url, featured
faqs                question, answer, category → FAQPage schema
distributor_leads   name, company, phone, email, state, lga, business_type,
                    monthly_order, locations, status pipeline
contact_leads       intent (order|retailer|info|complaint|distributor|partner),
                    message → email + notification
pages               About/brand-story sections, editable copy
```

**Naming canon (single source of truth):** *Elkris Oat Flour*, *Elkris Oat & Fibre Flour*, *Elkris Instant Plantain Flour*, *Elkris Poundo Yam Flour*. Never "Four", "Cartoon", "Super Oat" drift; use **Fibre** consistently (confirm against packaging).

---

## 6. Signature Interactive Features

1. **"What can I make with Elkris?" meal selector** — SWALLOW / BREAKFAST / BAKING / PORRIDGE tabs → matching products, "Perfect with" soups, recipes, Shop CTAs.
2. **"One product. Many possibilities" / How to Use It** — per-product visual tabs with prep micro-videos.
3. **Store Locator** — state/city filter + map + list; **GET DIRECTIONS** (deep link), **CALL STORE**, **VIEW ON MAP**; "Find Elkris Near Me" geolocation → nearest stores.
4. **WhatsApp-first ordering** — per-product *ORDER VIA WHATSAPP* → `wa.me` prefilled message: *"Hello Elkris Foods, I would like to order 2 packs of Elkris Oat Flour 1.3kg."* Floating WA button site-wide. No payments in scope.
5. **Product comparison** — "Which Elkris product is right for you?" table + FIND YOUR PRODUCT.
6. **Site search** — products, recipes, articles, stores, pages.
7. **Customer-service intent router (Contact)** — buttons for order / retailer / product info / complaint / distributor / retailer signup / partnership → correct form or prefilled WhatsApp.

---

## 7. Design System

- **Palette:** Elkris red + Elkris green + warm cream/white — replace neutral shadcn oklch tokens in `resources/css/app.css` (Tailwind v4 CSS-first `@theme`).
- **Type:** replace Instrument Sans with brand font (local/Bunny); fallback Inter/system until files supplied.
- **UI:** rounded cards, strong CTAs, subtle animations, high-quality food photography, Nigerian meal imagery. Site must match social ad creatives.
- **Mobile-first** (primary Meta/TikTok traffic): bottom action bar, generous tap targets, lazy video.
- **Media hygiene:** organize into `public/media/{logo,products,lifestyle,videos}/`; fix spaces/typos/double-space filenames.
- **Components to build (none exist):** `SiteHeader`, `SiteFooter`, `MobileBottomBar`, `WhatsAppFab`, plus public section components. Reuse existing shadcn/Radix kit + Headless UI.

---

## 8. Content & Compliance Rules

- **Zero template content** — no starter-kit copy, no `help@company.com`, no fake NY address. Guard with a CI grep test.
- **Canonical contact (Settings-managed):** info@elkrisfoods.com · Corporate Office: No. 14 Philomena Street, Santos Estate, Akowonjo, Egbeda, Lagos State, Nigeria · phone + WhatsApp number: **client input pending** (store in settings; hide WA FAB until number provided).
- **About rewrite:** WHO WE ARE / STORY / MISSION / VISION / BELIEVE / PRODUCTS / IMPACT / JOURNEY / TEAM — narrative brand story. Remove "2 million homes by 2025" (stale); re-baseline 2030 goal (client input).
- **Pricing:** verify all SKUs incl. ₦41,500 carton figure with client before launch.
- **Spelling:** Poundo Yam **Flour**, **Carton**, consistent Fibre naming.

---

## 9. Filament 5 Admin (`/admin`)

```
composer require filament/filament:"~5.0"
php artisan filament:install --panels
php artisan make:filament-user
```

**Resources:** Product (+variant repeater), Category, MealType, Recipe, Article, Store (lat/lng), GalleryItem, Testimonial, FAQ, DistributorLead (status pipeline), ContactLead (intent inbox), Settings (taglines, contact, socials, WA number, analytics IDs), Media via FileUpload; dashboard widgets (recent leads). Health-claims guidance banner on Article/Product forms.

---

## 10. Analytics, SEO, Growth (audit Phases 3–4)

- GA4 + Meta Pixel (+ later Conversions API, Google Ads) injected from Settings; `dataLayer` events: `page_view`, `view_item`, `begin_checkout` (WA click), `WhatsApp_click`, `phone_click`, `email_click`, `store_locator_click`, `distributor_submit`, `newsletter_signup`.
- SEO: titles/meta, Open Graph, `Product`/`Offer`, `FAQPage`, `Recipe` schema, sitemap.xml, robots, Search Console.
- Newsletter signups stored; automation later. Social embeds: FOLLOW ELKRIS row + "Latest from Elkris".

---

## 11. Phased Delivery (audit §33)

### Phase 1 — URGENT ✅ next
1. Design system: brand tokens, fonts, media organization
2. Public chrome: SiteHeader/nav, SiteFooter, MobileBottomBar, WhatsApp FAB
3. Kill all dummy content; canonical settings table
4. Products + variants + categories data model, seeders, dedicated product pages
5. Shop page (list, prices, single/carton, search/filter groundwork)
6. Homepage 13-section rebuild
7. About rewrite + brand story
8. Contact redesign + intent router
9. Where-to-Buy → store locator + cleaned retailer DB + Directions/Call/Map
10. WhatsApp ordering CTAs everywhere; naming/spelling cleanup; mobile pass
11. Filament admin for all of the above

### Phase 2 — CONVERSION
WhatsApp order flow formalization (order refs + `/orders/{number}`) · customer accounts · order tracking · product reviews · distributor portal pipeline · delivery info · cart-style WA order builder.

### Phase 3 — ENGAGEMENT
Recipe Hub (full) · Health hub · Video library · interactive meal selector + "Many possibilities" · product comparison · social integration · customer stories · newsletter automation.

### Phase 4 — MARKETING & ANALYTICS
GA4 · Meta Pixel · Conversions API · Google Ads · Search Console · full SEO/schema · retargeting · campaign landing pages · abandoned-cart recovery (only if real checkout added later).

Each phase must be independently shippable.

---

## 12. Engineering Standards

- **Wayfinder** for all route/action links (no hardcoded URLs).
- Public site = 100% Inertia/React; admin = Filament only.
- Feature tests: public routes render, store locator filters, WhatsApp message composition, contact/distributor validation + storage, no-template-content guard, FAQ/product schema. Factories for all models.
- Quality gates: `vendor/bin/pint --dirty` after PHP edits; `npm run lint`, `npm run types:check`, `npm run format:check`, `php artisan test`.
- Security: throttle contact/distributor forms; sanitize rich text; only public IDs (GA/Meta) in client; Filament behind auth.

## 13. Open Inputs from Client

1. Full phone number(s) + official WhatsApp business number
2. Confirmed product lineup, official names, verified prices
3. Brand font files / brand color hex values (or approval to derive from creatives)
4. Google Maps API key; GA4 & Meta Pixel IDs (can follow launch)
5. Updated About goals + team/impact photos
6. Full retailer list export (seed `stores`)
