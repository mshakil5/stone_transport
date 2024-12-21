


  var mycls = document.getElementsByClassName('nav-link');
  for(let i=0; i<=mycls.length; i++){
     mycls[i].addEventListener("click",function(){ 
      for (var j = 0; j < mycls.length; j++) {
        mycls[j].classList.remove('active');
      }
      this.classList.add('active');
    });
  }