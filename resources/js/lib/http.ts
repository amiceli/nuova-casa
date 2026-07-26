export type PostJsonParams = {
    url: string
    body?: Record<string, unknown>
}

function readXsrfToken(): string {
    const cookie = document.cookie.split("; ").find((item) => item.startsWith("XSRF-TOKEN="))

    return cookie ? decodeURIComponent(cookie.split("=")[1]) : ""
}

export async function postJson(params: PostJsonParams): Promise<Response> {
    return fetch(params.url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-XSRF-TOKEN": readXsrfToken(),
        },
        body: JSON.stringify(params.body ?? {}),
    })
}
