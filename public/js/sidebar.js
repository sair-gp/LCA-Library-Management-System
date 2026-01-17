

function toggleDropdownMenu(){
    const sidebar = document.getElementsByClassName('active')[0];
    console.log('dentro de la funcion');
    if (sidebar){
        console.log('Activo');
        let ulActiveHTML = `<li><a href="index.php?vista=pagineichon&pagina=1">Libreria</a></li>
                   <li><a href="#">Autores</a></li>
                   <li><a href="#">Editoriales</a></li>`;
        let ulActiveContent = document.getElementById('dropdown-menu');



    }
}