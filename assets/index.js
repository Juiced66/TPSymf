document.addEventListener('DOMContentLoaded', () => {

    let formCaravanes = document.querySelector('.cformHome')
    let formMobilHome = document.querySelector('.mformHome')
    let formEmplacement = document.querySelector('.tformHome')
        
    let champDateStartMobilHome = document.querySelector('.mstartAt')
    let champDateStartCaravane = document.querySelector('.cstartAt')
    let champDateStartEmplacement = document.querySelector('.tstartAt')
    
    let champDateEndMobilHome = document.querySelector('.mendAt')
    let champDateEndCaravane = document.querySelector('.cendAt')
    let champDateEndEmplacement = document.querySelector('.tsendAt')
    
    
    formCaravanes.addEventListener('submit', (e) =>
    {
        if (champDateEndCaravane.value < champDateStartCaravane.value ||
            champDateEndCaravane.value === '' ||
            champDateStartCaravane.value === '')
        {
            e.preventDefault()
        }
        
    })
    
    formMobilHome.addEventListener('submit', () =>
    {
        if (champDateEndMobilHome.value < champDateStartMobilHome.value ||
            champDateEndMobilHome.value === '' ||
            champDateStartMobilHome.value === '' )
        {
            e.preventDefault()
            
        }
    })
    
    formEmplacement.addEventListener('submit', () =>
    {
        if (champDateEndEmplacement.value < champDateStartEmplacement.value||
            champDateEndEmplacement.value === '' ||
            champDateStartEmplacement.value === '')
        {
            e.preventDefault()
            
        }
    })
})
// champDateEnd.forEach(element => {
    //     element.classList.contains('')
    // });
