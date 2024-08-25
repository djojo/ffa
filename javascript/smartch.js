// insert this function theme.js.liquid
window.onscroll = function(e) { 
    // alert("ok");
    posY = this.scrollY;
    if (posY > 0)
      document.getElementById('smartch-header').classList.add('partially_transparent');
    else 
    document.getElementById('smartch-header').classList.remove('partially_transparent');
}

// insert this class to theme.scss.liquid

// .partially_transparent {
//      background-color: rgba(num, num, num, 0.5);
// }