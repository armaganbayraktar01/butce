const puppeteer = require("puppeteer");
const fs = require("fs");


(async () => {
  const configPath = "json/bot-config.json";

  let userConfig = { categories: [] };
  if (fs.existsSync(configPath)) {
    userConfig = JSON.parse(fs.readFileSync(configPath, "utf-8"));
  }

  const keywordsArray = userConfig.categories.length > 0 ? userConfig.categories : ["Temel Gıda"];
  // const browser = await puppeteer.launch();
  // const page = await browser.newPage();
    const browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', // <-- Burayı kendi yoluna göre güncelle
    headless: true, // istersen false yapıp pencereyi görebilirsin
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
    const page = await browser.newPage();
    await page.goto("about:blank");

  const allData = [];
  const maxPages = 1000;
  const size = 24;

  for (const keyword of keywordsArray) {
    console.log(`🔍 Kategori: ${keyword}`);
    for (let currentPage = 0; currentPage < maxPages; currentPage++) {
      console.log(`➡️ Sayfa ${currentPage} çekiliyor...`);

      const response = await page.evaluate(async ({ currentPage, keyword, size }) => {
        const res = await fetch("https://api.marketfiyati.org.tr/api/v2/searchByCategories", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Origin": "https://marketfiyati.org.tr",
            "Referer": "https://marketfiyati.org.tr",
            "User-Agent": "Mozilla/5.0 (Linux; Android 6.0; Nexus 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36"
          },
          body: JSON.stringify({
            menuCategory: true,
            keywords: keyword,
            pages: currentPage,
            size: size,
            latitude: 38.33894991024116,
            longitude: 27.134700271034667,
            distance: 10,
            depots: [
              "migros-3672", "bim-C315", "a101-6191", "sok-1238", "bim-7819", "a101-E384",
              "sok-547", "a101-8270", "bim-G528", "sok-1734", "sok-9380", "sok-10877",
              "a101-9679", "bim-7807", "a101-6160", "migros-5628", "bim-Q238", "migros-9018",
              "carrefour-5304", "migros-8487", "migros-3842", "carrefour-3701",
              "carrefour-3684", "carrefour-3533", "carrefour-3680"
            ]
          })
        });
        return await res.json();
      }, { currentPage, keyword, size });

      if (!response.content || response.content.length === 0) {
        console.log(`⛔ Ürün yok, kategori ${keyword} sayfa ${currentPage} için durduruldu.`);
        break;
      }

      allData.push(...response.content);
      await new Promise(resolve => setTimeout(resolve, 500));

      // 🟩 Progress JSON güncelle
      fs.writeFileSync("json/progress.json", JSON.stringify({
        status: "progress",
        cekilenSayfa: currentPage + 1,
        toplamSayfa: maxPages,
        cekilenUrun: allData.length,
        kalanSure: `${(maxPages - currentPage - 1) * 0.5} sn`
      }, null, 2));

    }
  }

  fs.writeFileSync("json/veri.json", JSON.stringify(allData, null, 2));
  console.log(`✅ Toplam ${allData.length} ürün veri.json dosyasına yazıldı.`);
  await browser.close();

    // ✅ DONE DURUMU SADECE EN SONA YAZ
    fs.writeFileSync("json/progress.json", JSON.stringify({
      status: "done",
      cekilenUrun: allData.length
    }, null, 2));

})();
