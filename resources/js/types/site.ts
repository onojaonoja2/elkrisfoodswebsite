export type Variant = {
    id: number;
    product_id: number;
    size_label: string;
    pack_type: 'single' | 'carton';
    sku: string | null;
    is_available: boolean;
};

export type Category = {
    id: number;
    name: string;
    slug: string;
    image_path: string | null;
    description?: string | null;
};

export type MealType = {
    id: number;
    name: string;
    slug: string;
    usage_copy?: string | null;
    products?: Pick<
        Product,
        'id' | 'name' | 'slug' | 'hero_image' | 'category_id' | 'best_with'
    >[];
};

export type Product = {
    id: number;
    category_id: number;
    name: string;
    slug: string;
    tagline: string | null;
    description: string | null;
    benefits: string[] | null;
    best_with: string[] | null;
    nutrition_notes: string | null;
    whatsapp: string | null;
    meta_title: string | null;
    meta_description: string | null;
    og_image: string | null;
    hero_image: string | null;
    gallery: string[] | null;
    sort_order: number;
    is_published: boolean;
    is_featured: boolean;
    category?: Category | null;
    variants?: Variant[];
    mealTypes?: MealType[];
    recipes?: Recipe[];
    created_at?: string;
    updated_at?: string;
};

export type Recipe = {
    id: number;
    product_id: number | null;
    title: string;
    slug: string;
    category: string;
    image_path: string | null;
    prep_minutes: number;
    servings: number;
    difficulty: string;
    ingredients: string[] | null;
    steps: string[] | null;
    tips: string | null;
    summary: string | null;
    meta_title: string | null;
    meta_description: string | null;
    og_image: string | null;
    is_published: boolean;
    product?: Pick<Product, 'id' | 'name' | 'slug' | 'whatsapp'> | null;
};

export type Article = {
    id: number;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    body_html?: string;
    cover_path: string | null;
    author: string | null;
    reading_minutes: number;
    meta_title: string | null;
    meta_description: string | null;
    og_image: string | null;
    is_published?: boolean;
    published_at: string | null;
};

export type GalleryItem = {
    id: number;
    title: string;
    caption: string | null;
    category: string;
    media_type: 'image' | 'video';
    media_path: string;
    media_url: string;
    sort_order?: number;
};

export type AboutCoreValue = {
    title: string;
    description: string;
    icon: string;
};

export type AboutContent = {
    who_we_are: string[];
    vision: string;
    mission: string;
    values_intro: string;
    core_values: AboutCoreValue[];
};

export type SearchType = 'product' | 'recipe' | 'article' | 'store';

export type SearchResult = {
    type: SearchType;
    title: string;
    subtitle: string | null;
    url: string;
};

export type Store = {
    id: number;
    name: string;
    address: string;
    city: string;
    state: string;
    phone: string | null;
    opening_hours: string | null;
    latitude: number | null;
    longitude: number | null;
    products_available: string[] | null;
    directions_url?: string;
};

export type Faq = {
    id: number;
    question: string;
    answer: string;
    category: string;
};

export type Testimonial = {
    id: number;
    name: string;
    location: string | null;
    quote: string;
    photo_path: string | null;
    video_url: string | null;
    is_featured: boolean;
};

export type OrderItem = {
    id: number;
    product_id: number;
    variant_id: number;
    product_name: string;
    size_label: string;
    pack_type: string;
    quantity: number;
};

export type OrderStatus =
    'pending' | 'confirmed' | 'ready' | 'delivered' | 'cancelled';

export type Order = {
    id: number;
    number: string;
    name: string;
    phone: string;
    email: string | null;
    notes: string | null;
    status: OrderStatus;
    created_at: string;
    items: OrderItem[];
};

export type Review = {
    id: number;
    name: string;
    rating: number;
    body: string;
    created_at: string;
};

export type RatingSummary = {
    average: number;
    count: number;
};

export type DistributorLeadStatus =
    'new' | 'in_progress' | 'approved' | 'rejected' | 'archived';

export type DistributorLead = {
    reference: string;
    status: DistributorLeadStatus;
    created_at?: string;
};

export type ContentNavItem = {
    label: string;
    url: string;
};

export type FooterColumn = {
    title: string;
    links: ContentNavItem[];
};

export type GlobalContent = {
    brand: {
        site_name: string;
        logo: string;
        favicon: string;
        apple_touch_icon: string;
        default_og_image: string;
        default_description: string;
    };
    nav: {
        items: ContentNavItem[];
        account_label: string;
        search_label: string;
        shop_label: string;
        theme_light_label: string;
        theme_dark_label: string;
        menu_open_label: string;
        menu_close_label: string;
    };
    footer: {
        columns: FooterColumn[];
        follow_heading: string;
        tagline_fallback: string;
        positioning_fallback: string;
        copyright: string;
    };
    newsletter: {
        heading: string;
        body: string;
        placeholder: string;
        button: string;
        processing: string;
        success_heading: string;
        success_body: string;
    };
    share: {
        label: string;
        copied: string;
        copy: string;
    };
    mobile: {
        home: string;
        shop: string;
        stores: string;
        whatsapp: string;
        account: string;
        aria_label: string;
    };
    whatsapp: {
        fab_label: string;
        general_message: string;
        order_message: string;
    };
    product_card: {
        no_image: string;
        view: string;
    };
    testimonials: {
        rating_label: string;
        aria_label: string;
    };
};

export type PageContent = Record<string, unknown>;

export type LabelsContent = {
    order_statuses: Record<string, string>;
    distributor_statuses: Record<string, string>;
    contact_intents: Record<string, string>;
    business_types: Record<string, string>;
    monthly_orders: Record<string, string>;
};

export type SiteContent = {
    global: GlobalContent;
    labels: LabelsContent;
    [key: string]: unknown;
};

export type SettingsMap = {
    'brand.primary_tagline'?: string | null;
    'brand.positioning_tagline'?: string | null;
    'brand.positioning_line'?: string | null;
    'contact.email'?: string | null;
    'contact.phone'?: string | null;
    'contact.address'?: string | null;
    'contact.whatsapp'?: string | null;
    'delivery.info'?: string | null;
    'social.facebook'?: string | null;
    'social.instagram'?: string | null;
    'social.tiktok'?: string | null;
    'social.youtube'?: string | null;
    'social.x'?: string | null;
    'analytics.ga4_id'?: string | null;
    'analytics.meta_pixel_id'?: string | null;
};

export type SharedProps = {
    name: string;
    auth: {
        user: { id: number; name: string; email: string; role?: string } | null;
    };
    settings?: SettingsMap;
    content: SiteContent;
    sidebarOpen?: boolean;
    flash?: { success?: string; error?: string; wa_url?: string | null };
};
