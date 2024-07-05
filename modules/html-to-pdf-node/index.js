var fs = require("fs");
var path = require("path");
var https = require("https");
var express = require("express");
var html_to_pdf = require("html-pdf-node");
var app = express();

var ssl_key, ssl_cert = false;

fs.readdir("/var/www/httpd-cert/", (err, files) => {
    files.forEach((file) => {
        if (file.startsWith("niuedu.uz_")) {
            if (path.extname(file) == ".key") {
                ssl_key = fs.readFileSync("/var/www/httpd-cert/" + file);
            } else if (path.extname(file) == ".crt") {
                ssl_cert = fs.readFileSync("/var/www/httpd-cert/" + file);
            }

            if (ssl_key && ssl_cert) {
                var options = {
                    key: ssl_key,
                    cert: ssl_cert
                };
    
                var port = 4499;

                // console.log(options);
    
                var server = https.createServer(options, app);
    
                app.get("/*", (req, res) => {
                    res.set('Content-Type', 'application/pdf');
                    res.set('Content-Disposition', `attachment; filename=shartnoma.pdf`);
                
                    let options2 = { format: 'A4' };
                    var shartnoma_id = req.path.replace("/", "").replace(".pdf", "");
                    let file2 = { url: "https://niuedu.uz/shartnoma_html/" + shartnoma_id };
                    // let file = {content: '<html><head></head><body><h1>Hello World</h1></body></html>'};
                    console.log({id: shartnoma_id});
                    html_to_pdf.generatePdf(file2, options2).then(pdfBuffer => {
                        res.send(pdfBuffer);
                    });
                });
                
                server.listen(port, () => {
                    console.log("listening on *:" + port);
                });
            }
        }
    });
});