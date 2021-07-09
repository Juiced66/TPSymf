document.addEventListener('DOMContentLoaded', () => {

    let tableauDeJournee = document.querySelectorAll('.table');
    let btnsDetail= document.querySelectorAll('.btnDetail')
    let btnsReduire= document.querySelectorAll('.reduireTable')
    let rows = document.querySelectorAll('.row')

    console.log(tableauDeJournee);
    console.log(btnsDetail);

    for (let i = 0; i < btnsDetail.length; i++) {
        console.log(tableauDeJournee[i])
        const element = btnsDetail[i];
        element.addEventListener('click', () =>
        {
            
            if(tableauDeJournee[i].classList.contains('hidden'))
            {
                tableauDeJournee[i].classList.remove('hidden')
                rows[i].classList.add('hidden')
                return
            } 
            tableauDeJournee[i].classList.add('hidden')
            rows[i].classList.remove('hidden')

        })
            
    }

    for (let i = 0; i < btnsReduire.length; i++) {
        const element = btnsReduire[i];
        element.addEventListener('click', () =>
        {
            
            if(tableauDeJournee[i].classList.contains('hidden'))
            {
                tableauDeJournee[i].classList.remove('hidden')
                rows[i].classList.add('hidden')
                return
            } 
            tableauDeJournee[i].classList.add('hidden')
            rows[i].classList.remove('hidden')

        })
            
    }

// console.log(tableauDeJournee)
    
//     btnsDetail.map((element, i = 0)  => 
//     {
//         console.log( tableauDeJournee[i]);
//         element.addEventListener('click', () =>
//         {
            
//             if(tableauDeJournee[i].classList.contains('hidden'))
//             {
//                 tableauDeJournee[i].classList.remove('hidden')
//                 return
//             } 
//             tableauDeJournee[i].classList.add('.hidden')
//             // else 
//             // {
//             //     element.classList.add('hidden')
//             //     tableauDeJournee[i].classList.remove('.hidden')
//             // }
//         })
//     })
})