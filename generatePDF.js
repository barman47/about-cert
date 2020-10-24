// 'use strict';
// NOTE:: Don't use any console.log as the server will see it as an error response
const puppeteer = require('puppeteer');

let arguments = process.argv.slice(2); 

let url = "";
try{
  url = arguments.findIndex(el => el == "--url") < 0 ? "" : arguments[arguments.findIndex(el => el == "--url") + 1];
}catch(e){
  console.log("The url must be placed after the --url flag");
  process.exit(1);
}

let path = "";
try{
  path = arguments.findIndex(el => el == "--path") < 0 ? "" : arguments[arguments.findIndex(el => el == "--path") + 1];
}catch(e){
  console.log("The path must be placed after the --path flag");
  process.exit(1);
}


const generatePDF = async () => {
  try {
    const browser = await puppeteer.launch({
        pipe: true,
        headless: true,
        ignoreHTTPSErrors: true,
        timeout: 120000,
        args: ['--headless', '--disable-gpu', '--full-memory-crash-report', '--unlimited-storage',
               '--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });
    
    // const [page] = await browser.pages();
    page = await browser.newPage();

    // await page.goto(url);
    await page.goto(url, {waitUntil: 'networkidle0'});

    // console.log(await page.evaluate(() => test()));

    await page.pdf({
        path: path,
        format: 'A4',
        printBackground: true
    });

    // await page.screenshot({
    //   path: path + ".png",
    //   printBackground: true
    // });
    await page.close();
    await browser.close();
  } catch (err) {
    console.error(err);
    // console.log("An error has occured");
    process.exit(1);
  }
};

generatePDF();
// console.log(url)
// console.log(!!puppeteer);