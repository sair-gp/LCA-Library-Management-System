const notificationBell = document.querySelector(".notification-bell");
const popup = document.getElementById("popup");
let isOpen = false;

// Función para agregar una nueva notificación
function addNotification(title, text, link = "") {
  const notificationList = document.getElementById("notification-list");

  // Crear un elemento de notificación
  const notification = document.createElement("li");

  // Crear contenido dinámico
  let notificationContent = `<strong>${title}</strong>: ${text}`;
  if (link) {
    notificationContent += ` <br> <a href="${link}">Ver préstamo</a>`;
  }

  // Asignar el contenido al elemento
  notification.innerHTML = notificationContent;

  notificationList.appendChild(notification);

  // Incrementar el contador de notificaciones
  const notificationCount = document.getElementById("notification-count");
  const currentCount = parseInt(notificationCount.textContent, 10) || 0;
  notificationCount.textContent = currentCount + 1;

  // Asegurarse de que el badge sea visible
  if (currentCount === 0) {
    notificationCount.style.display = "inline-block";
  }
}

notificationBell.addEventListener("click", () => {
  if (!isOpen) {
    popup.style.display = "block";
    isOpen = true;
  } else {
    popup.style.display = "none";
    isOpen = false;
  }
});
