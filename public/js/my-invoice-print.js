jsPrintSetup.setOption('orientation', jsPrintSetup.kPortraitOrientation);

jsPrintSetup.setOption('marginTop', 0);
jsPrintSetup.setOption('marginBottom', 0);
jsPrintSetup.setOption('marginLeft', 0);
jsPrintSetup.setOption('marginRight', 0);

jsPrintSetup.setOption('headerStrLeft', '');
jsPrintSetup.setOption('headerStrCenter', '');
jsPrintSetup.setOption('headerStrRight', '');

jsPrintSetup.setOption('footerStrLeft', '');
jsPrintSetup.setOption('headerStrCenter', '');
jsPrintSetup.setOption('headerStrRight', '');

jsPrintSetup.setOption('scaling', 100);
jsPrintSetup.setOption('paperData', 27);
jsPrintSetup.setOption('numCopies', 2);

jsPrintSetup.clearSilentPrint();
jsPrintSetup.setOption('printSilent', 1);

jsPrintSetup.print();