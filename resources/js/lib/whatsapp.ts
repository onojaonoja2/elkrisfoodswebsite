export function buildWhatsAppUrl(number: string, message: string): string {
    const digits = number.replace(/[^\d]/g, '');
    const withCountry = digits.startsWith('234')
        ? digits
        : `234${digits.replace(/^0/, '')}`;

    return `https://wa.me/${withCountry}?text=${encodeURIComponent(message)}`;
}

export function productOrderMessage(
    template: string,
    productName: string,
    quantity: number,
    sizeLabel?: string,
): string {
    const size = sizeLabel ? ` (${sizeLabel})` : '';

    return template
        .replaceAll('{quantity}', String(quantity))
        .replaceAll('{product}', productName)
        .replaceAll('{size}', size);
}

export function generalWhatsAppMessage(
    settingsWhatsApp?: string | null,
): string {
    void settingsWhatsApp;

    return 'Hello Elkris Foods, I would like to make an enquiry.';
}
