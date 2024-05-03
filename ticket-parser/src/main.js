const ZebraCrossing = require('zebra-crossing');
const fs = require('fs');

(async () => {
    const ticketData = await ZebraCrossing.read(fs.readFileSync('../storage/app/screenshot.jpeg'), {
        pureBarcode: true,
        possibleFormats: ['AZTEC']
    });

    fs.writeFileSync('../storage/app/screenshot.bin', ticketData.raw);

    console.log('screenshot.bin');
})();
