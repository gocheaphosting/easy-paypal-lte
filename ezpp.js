function popUp(URL) {
    id = 820 ;
    eval("page = window.open(URL, '" + id +
         "','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=830,height=780,left = 10,top = 10');");
    if (page == null) {
        alert('Error while launching new window! Your browser maybe blocking popups.'
              + '\nPlease allow popups from this web site to see this content.'
              + '\nOr, use <Control><Alt> click on the link to temporarily allow the popup.') ;
    }
    else {
        pageFat.focus() ;
    }
}