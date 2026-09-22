import puppeteer from "puppeteer";

const couponsToGenerate = parseInt(process.argv[2] || "1");

const generateCoupons = async () => {
    const browser = await puppeteer.launch({ headless: true, args: ["--no-sandbox"] });
    const page = await browser.newPage();

    await page.goto("http://downselfie.com/payment/mobile/goods_start", { waitUntil: "networkidle2" });

    const coupons = [];

    for (let i = 0; i < couponsToGenerate; i++) {
        await page.click(".btn_buy");
        await page.waitForTimeout(1000);

        await page.click("#PRODUCT_104");
        await page.waitForTimeout(2000);

        await page.evaluate(() => window.fnEWalletPayment("FREE"));
        await page.waitForTimeout(4000);

        const coupon = await page.$eval(".coupon_area input", el => el.value);
        coupons.push(coupon);
        console.log(coupon);
    }

    await browser.close();
};

generateCoupons();