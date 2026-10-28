const express = require('express');
const puppeteer = require('puppeteer');
const updateEverything = require("./sync");
const app = express();
let updating = false;
let status = "Idle";
app.use(express.json());

const MAX_PARALLEL = 4;

async function processOrder(browser, { OrderId, type, count, kode }, idx) {
    const result = { idx, status: '', kupon: null, step: '', payment: '' };
    let page;

    try {
        page = await browser.newPage();
        await page.setRequestInterception(true);
        page.on('request', (route) => {
          if (['image', 'stylesheet', 'font', 'media'].includes(route.resourceType())) {
            route.abort();
          } else {
            route.continue();
          }
        });
                page.setDefaultTimeout(30000);
                page.setDefaultNavigationTimeout(30000)
        result.step = 'goto_page';
        await page.goto("http://downselfie.com/payment/mobile/goods_view_setp_2", {
            waitUntil: "domcontentloaded"
        });

        result.step = 'wait_fnSubmitNextSetp';
        await page.waitForFunction(() => typeof window.fnSubmitNextSetp === 'function');

        result.step = 'submit_form';

        // ðŸ”¥ WAJIB: handle navigation biar ga "Execution context destroyed"
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
            page.evaluate(({ type, count }) => {
                $('#print_count').val(count);
                $('#sel_frame').val("PRODUCT_" + type);
                $('#print_type').val(type);
                $('#form1')[0].submit();
            }, { type, count })
        ]);

        result.step = 'click_chk';
        await page.waitForSelector("#chk", { timeout: 15000 });

        result.payment = await page.evaluate(() => {
            return document.querySelector('#txTotalPayment')?.value || null;
        });

        result.step = 'wait_fnEWalletPayment';
        await page.waitForFunction(() => typeof window.fnEWalletPayment === 'function', { timeout: 15000 });

        result.step = 'fnEWalletPayment';
        await page.evaluate(() => fnEWalletPaymentSubmit("FREE"));

        result.step = 'wait_coupon';
        await page.waitForSelector(".coupon_area", { timeout: 20000 });

        const kupon = await page.$eval(".coupon_area input[type='text']", el => el.value);

        result.status = 'success';
        result.kupon = kupon;
        result.idx = OrderId;
        result.type = type;

    } catch (err) {
        result.status = 'error';
        result.error = err.message;
    } finally {
        // âœ… SAFE CLOSE (ANTI CRASH)
        if (page && !page.isClosed()) {
            try {
                await page.close();
            } catch (e) {}
        }
    }

    return result;
}
app.post('/generate', async (req, res) => {
    const orders = req.body.orders || [];
    if (!orders.length) return res.status(400).json({ error: 'No orders provided' });

    const browser = await puppeteer.launch({
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--disable-software-rasterizer',
            '--disable-extensions',
            '--disable-background-networking',
            '--disable-sync',
            '--metrics-recording-only',
            '--mute-audio',
            '--no-first-run',
            '--no-zygote',
            '--single-process',
            '--disable-features=site-per-process',
            '--disable-features=IsolateOrigins',
            '--disable-site-isolation-trials',
            '--disable-features=HttpsFirstBalancedModeAutoEnable'
        ]
    });

    const results = [];

    // âœ… LOOP SATU-SATU (NO PARALLEL)
    for (let i = 0; i < orders.length; i++) {
        const order = orders[i];

        console.log(`Processing order ${i}`);

        const result = await processOrder(browser, order, i);

        results.push(result);

        // ðŸ”¥ optional delay biar lebih aman (anti rate limit / anti crash)
        await new Promise(resolve => setTimeout(resolve, 1000));
    }

    await browser.close();

    res.json(results);
});
app.get('/health', (req, res) => {
    res.json({ status: 'ok' });
});

app.get("/update", (req, res) => {
    if (updating) {
        return res.json({ status: "already_running" });
    }

    updating = true;
    status = "Updating";

    res.json({ status: "started" });

    updateEverything()
        .then(() => {
            status = "Idle";
        })
        .catch(err => {
            console.error(err);
            status = "Failed";
        })
        .finally(() => {
            updating = false;
        });
});
app.get("/status", (req, res) => {
    res.json({
        updating,
        status
    });
});

const PORT = process.env.PORT || 3000;
app.listen(3000, () => {
    console.log(`Running on ${PORT}`);
});