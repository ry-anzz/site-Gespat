function showSection(sectionId) {
    // Esconde todas as seções
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.style.display = 'none'; // Esconder todas as seções
    });

    // Exibe a seção selecionada
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block'; // Exibe a seção clicada
    }

    // Remove a classe 'active' de todos os links de navegação
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        link.classList.remove('active'); // Remover a classe 'active'
    });

    // Adiciona a classe 'active' ao link correspondente
    const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active'); // Adicionar a classe 'active' ao link clicado
    }
}

window.onload = function() {
    showSection('cadastrar'); }// Chame a função para mostrar a seção 'cadastrar'