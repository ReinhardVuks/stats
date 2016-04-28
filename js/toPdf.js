function demoFromHTML() {
    var pdf = new jsPDF('p', 'pt', 'letter');
    var imgData = '.............';
    pdf.setFontSize(40);
    pdf.addImage(imgData, 'PNG', 12, 30, 130, 40);
    pdf.cellInitialize();
    pdf.setFontSize(10);
    $.each($('#customers tr'), function (i, row) {
        $.each($(row).find("th"), function (j, cell) {
            var txt = $(cell).text();
            var width = (j == 4) ? 300 : 300; //make with column smaller
            pdf.cell(10, 30, width, 70, txt, i);
        });
        $.each($(row).find("td"), function (j, cell) {
            var txt = $(cell).text().trim() || " ";
            var width = (j == 4) ? 200 : 300; //make with column smaller
            pdf.cell(10, 50, width, 30, txt, i);
        });

    });
    pdf.save('sample-file.pdf');
}