function setCookie(name, value) {
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value)
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))

    return matches ? decodeURIComponent(matches[1]) : undefined
}

function deleteCookie(name) {
    setCookie(name, "")
}

function logger() {
    if (location.href.includes('/login.html')) {
        if (getCookie("login")) {
            location.replace("/")
        }
    } else {
        if (!getCookie("login")) {
            location.replace("/login.html")
        }
    }
}

export {
    setCookie,
    getCookie,
    deleteCookie,
    logger
}
