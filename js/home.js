const playButton = document.querySelector('.controller[data-play]')
const logOutButton = document.querySelector('.controller[data-log-out]')

playButton.addEventListener('click', () => {
    window.location.href = "/pages/waiting-room.php"
})

logOutButton.addEventListener('click', () => {
    window.location.href = "/pages/logout.php"
})
