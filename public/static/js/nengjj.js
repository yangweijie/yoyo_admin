"use strict";
var nengjj = function () {
    var e = Math.floor;

    function a() {
        for (var a = "23456789abcdefghijkmnpqrstuvwxyz", b = "", c = 0; c < 8; c++) b += a.charAt(e(Math.random() *
            a.length));
        return b
    }

    function b(a, b) {
        var c = new XMLHttpRequest;
        c.open(b.method, a), c.setRequestHeader("Content-Type", b.contentType), c.onreadystatechange = function (a) {
            var c = a.target;
            if (c.readyState === XMLHttpRequest.DONE)
                if (200 === c.status) {
                    var d = JSON.parse(c.responseText);
                    b.onsuccess && "function" == typeof b.onsuccess && b.onsuccess(d.data)
                } else {
                    var e = "\u7F51\u7EDC\u9519\u8BEF";
                    if (0 != c.status) {
                        var d = JSON.parse(c.responseText);
                        e = d.data
                    }
                    b.onerror && "function" == typeof b.onerror && b.onerror(c.status, e)
                }
        };
        var d = null;
        b.data && (d = JSON.stringify(b.data)), c.send(d)
    }

    function c(a, b) {
        try {
            var c = new WebSocket(a);
            c.onopen = function () {
                console.log("WebSocket CONNECTED")
            }, c.onclose = function () {
                console.log("WebSocket DISCONNECTED")
            }, b && "function" == typeof b && (c.onmessage = b), c.onerror = function (a) {
                console.log("WebSocket ERROR: " + a.data)
            }
        } catch (a) {
            console.log("WebSocket ERROR: " + a)
        }
        return c
    }

    function d(a) {
        var b = JSON.parse(a.data),
            c = new CustomEvent("nengjj_event_print", {
                detail: b.data
            });
        document.dispatchEvent(c)
    }
    var f = {};
    return f.getInstance = function (e) {
        function f() {
            g.wss && 1 !== g.wss.readyState && 0 !== g.wss.readyState && (g.wss = c(g.wssHost + "/event/" + g.id,
                d))
        }
        var g = {},
            h = "https://",
            i = "wss://";
        e && "off" == e.ssl && (h = "http://", i = "ws://");
        var j = 20100;
        e && e.port && (j = e.port);
        var k = "localhost";
        return e && e.host && (k = e.host), g.host = h + k + ":" + j, g.wssHost = i + k + ":" + (j + 1), g.id =
            a(), g.wss = c(g.wssHost + "/event/" + g.id, d), g.getInfo = function (a, c) {
                a && console.assert("function" == typeof a), c && console.assert("function" == typeof c), f();
                var d = g.host + "/info";
                b(d, {
                    method: "GET",
                    contentType: "application/json",
                    data: null,
                    onsuccess: a,
                    onerror: c
                })
            }, g.installLicense = function (a, c, d) {
                c && console.assert("function" == typeof c), d && console.assert("function" == typeof d), f();
                var e = g.host + "/license";
                b(e, {
                    method: "POST",
                    contentType: "application/json",
                    data: {
                        token: a
                    },
                    onsuccess: c,
                    onerror: d
                })
            }, g.readLicense = function (a, c) {
                a && console.assert("function" == typeof a), c && console.assert("function" == typeof c), f();
                var d = g.host + "/license";
                b(d, {
                    method: "GET",
                    contentType: "application/json",
                    data: null,
                    onsuccess: a,
                    onerror: c
                })
            }, g.print = function () {
                function c(c, d, e, h) {
                    e && console.assert("function" == typeof e), h && console.assert("function" == typeof h), f(),
                        c.clientId = g.id, c.docId = a(), c.mode = d;
                    var i = g.host + "/mod-print/print";
                    return b(i, {
                        method: "POST",
                        contentType: "application/json",
                        data: c,
                        onsuccess: e,
                        onerror: h
                    }), c.docId
                }
                var d = {};
                return d.listPrinter = function (a, c) {
                    a && console.assert("function" == typeof a), c && console.assert("function" == typeof c),
                        f();
                    var d = g.host + "/mod-print/printer";
                    b(d, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: a,
                        onerror: c
                    })
                }, d.getDefaultPrinter = function (a, c) {
                    a && console.assert("function" == typeof a), c && console.assert("function" == typeof c),
                        f();
                    var d = g.host + "/mod-print/printer-default";
                    b(d, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: a,
                        onerror: c
                    })
                }, d.listPrinterPaper = function (a, c, d) {
                    c && console.assert("function" == typeof c), d && console.assert("function" == typeof d),
                        f();
                    var e = g.host + "/mod-print/printer-paper/" + encodeURIComponent(a);
                    b(e, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: c,
                        onerror: d
                    })
                }, d.listPrinterJob = function (a, c, d) {
                    c && console.assert("function" == typeof c), d && console.assert("function" == typeof d),
                        f();
                    var e = g.host + "/mod-print/printer-job/" + encodeURIComponent(a);
                    b(e, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: c,
                        onerror: d
                    })
                }, d.getPrinterState = function (a, c, d) {
                    c && console.assert("function" == typeof c), d && console.assert("function" == typeof d),
                        f();
                    var e = g.host + "/mod-print/printer-state/" + encodeURIComponent(a);
                    b(e, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: c,
                        onerror: d
                    })
                }, d.getPrinterInfo = function (a, c, d) {
                    c && console.assert("function" == typeof c), d && console.assert("function" == typeof d),
                        f();
                    var e = g.host + "/mod-print/printer-info/" + encodeURIComponent(a);
                    b(e, {
                        method: "GET",
                        contentType: "application/json",
                        data: null,
                        onsuccess: c,
                        onerror: d
                    })
                }, d.print = function (a, b, d) {
                    return c(a, "print", b, d)
                }, d.printConfig = function (a, b, d) {
                    return c(a, "config", b, d)
                }, d.printPreview = function (a, b, d) {
                    return c(a, "preview", b, d)
                }, d
            }(), g
    }, f
}();