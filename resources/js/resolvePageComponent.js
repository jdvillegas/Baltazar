export function resolvePageComponent(name, pagesDirectory) {
    const pages = pagesDirectory();
    const page = pages[`./Pages/${name}.vue`];

    if (!page) {
        throw new Error(`Could not resolve page component: ${name}`);
    }

    return page;
}
