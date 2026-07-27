export enum TagError {
    NameRequired = "tag_name_required",
    NameInvalid = "tag_name_invalid",
    NameAlreadyUsed = "tag_name_already_used",
    IconRequired = "tag_icon_required",
    IconInvalid = "tag_icon_invalid",
    CheckFailed = "tag_check_failed",
    SaveFailed = "tag_save_failed",
}

export type Page = {
    id: number
    created_at: Date
    updated_at: Date
    url: string
    title: string
    icon: string
    favorite: boolean
    parent: {
        id: number
        name: string
        color: string
    }
}

export type Tag = {
    id: number
    created_at: Date
    updated_at: Date
    url: string
    name: string
    icon: string
    color: string
    children: Array<Pick<Page, "id" | "icon" | "title" | "url" | "favorite" | "created_at">>
}

export enum ImportError {
    FileRequired = "import_file_required",
    FileInvalid = "import_file_invalid",
    FileTooLarge = "import_file_too_large",
    NoBookmarkFound = "import_no_bookmark_found",
    UploadFailed = "import_upload_failed",
}

export enum ThemeError {
    Required = "theme_required",
    Invalid = "theme_invalid",
    SaveFailed = "theme_save_failed",
}

/**
 * What the server answers once the tags and the links are in place, the icons
 * are still being looked for at that point.
 */
export type StartedImport = {
    importId: string
    tags: number
    pages: number
    skipped: number
    total: number
}

export type ImportProgress = {
    importId: string
    done: number
    total: number
}

export type ImportResult = {
    importId: string
    tags: number
    pages: number
}

export enum NewsletterError {
    UrlRequired = "newsletter_url_required",
    AlreadyFollowed = "newsletter_already_followed",
    FeedNotFound = "newsletter_feed_not_found",
    CatalogLoadFailed = "newsletter_catalog_load_failed",
    SaveFailed = "newsletter_save_failed",
}

export type NewsletterItem = {
    title: string
    link: string
    date: string
}

export type Newsletter = {
    id: number
    created_at: Date
    updated_at: Date
    url: string
    title: string
    icon: string | null
    isRead: boolean
    lastReadAt: string | null
    lastLink: NewsletterItem | null
}

export type AvailableNewsletter = {
    id: number
    name: string
    url: string
    feedUrl: string | null
    description: string | null
    author: string | null
    authorUrl: string | null
    category: string | null
    icon: string | null
    followed: boolean
}
