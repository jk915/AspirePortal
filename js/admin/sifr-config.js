var main = {
  src: base_url+'flash/admin/cg.swf'
};

var cg = {
  src: base_url+'flash/admin/cg.swf'
};

sIFR.activate(main); //

sIFR.replace(main, {
  selector: '#nav h1'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 16px; color: #FFFFFF; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  });

sIFR.replace(main, {
  selector: 'h1'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 21px; color: #333333; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  }); 
  
sIFR.replace(main, {
  selector: 'h2'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 16px; color: #333333; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  });   
  
sIFR.replace(main, {
  selector: '#sidebar h3'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 21px; color: #003399; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  }); 
  
sIFR.replace(main, {
  selector: 'h3'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 16px; color: #003399; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  });    
  
sIFR.replace(main, {
  selector: 'h4'
    ,css: [
      '.sIFR-root { text-align: left; font-weight: normal; font-size: 12px; color: #003399; margin: 0; padding: 0;}'
    ], wmode: 'transparent'
  }); 