let panel = document.getElementById('panel');
let panelContent = document.getElementById('panel-content');
let menu = document.getElementById('menu');

function togglePanel(){
  if(panel.classList.contains('hidden')) {
      panel.classList.remove('hidden');
      menu.classList.add('hidden');
      panelContent.classList.add('open');
     // panelContent.style.width = '275px';

  } else {
        panel.classList.add('hidden');
         menu.classList.remove('hidden');
      panelContent.classList.remove('open');

      //panelContent.style.width = '0';

  }
}
menu.addEventListener('click', togglePanel);
panel.addEventListener('click', togglePanel);
