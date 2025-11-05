// Seleciona todos os itens de menu que têm um submenu
const dropdownItems = document.querySelectorAll('.item-opcao');

dropdownItems.forEach(item => {
  const link = item.querySelector('.link-opcao');
  const submenu = item.querySelector('.submenu');

  // Continua apenas se o item realmente tiver um submenu
  if (submenu) {
    link.addEventListener('click', (event) => {
      // Previne o link de navegar para '#'
      event.preventDefault(); 
      
      // Adiciona ou remove a classe 'active' no elemento pai (o <li>)
      item.classList.toggle('active');
    });
  }
});