document.addEventListener('DOMContentLoaded', () => {

    let tableauDeJournee = document.querySelectorAll('.table');
    let btnsDetail= document.querySelectorAll('.btnDetail')
    let btnsReduire= document.querySelectorAll('.reduireTable')
    let journees = document.querySelectorAll('.journee')
    let pannels = document.querySelectorAll('.pannel')
    let btnsPiscine = document.querySelectorAll('.btnPiscine')
    let sommeTotal = document.querySelector('.sommeTotal')
    let piscinesEnfant = document.querySelectorAll('.piscineEnfant')
    let piscinesAdulte = document.querySelectorAll('.piscineAdulte')
    let tBodies = document.querySelectorAll('tbody')
    const prixBasique = document.querySelectorAll('.sommeJournee')
    let ancienneValeur = 0
    function ajouterLigneTab(label, prix, parent, classes, quantite = null) 
    {
        let ligne = document.createElement('tr')
        let cube = document.createElement('td')
        let cube1 = document.createElement('td')
        let cube2 = document.createElement('td')

        if(quantite != null)
        {
            ligne.classList.add(classes)
            ligne.setAttribute('data-previous', prix)
            cube.innerHTML=`${label}`
            ligne.appendChild(cube)

            cube1.innerHTML=`${quantite}`
            ligne.appendChild(cube1)

            cube2.innerHTML=`${prix}`
            ligne.appendChild(cube2)

            parent.appendChild(ligne)
            return
        }
        ligne.classList.add(classes)
        ligne.setAttribute('data-previous', prix)
        cube.innerHTML=`${label}`
        cube.setAttribute('colspan', '2')

        ligne.appendChild(cube)
        cube2.innerHTML=`${prix}`
        
        ligne.appendChild(cube2)
        parent.appendChild(ligne)

    }



    for (let i = 0; i < btnsReduire.length; i++) 
    {
        const btnReduire = btnsReduire[i]
        const btnPiscine = btnsPiscine[i]
        const btnDetail = btnsDetail[i]
        const piscineEnfant = piscinesEnfant[i]
        const piscineAdulte = piscinesAdulte[i]

        btnReduire.addEventListener('click', () =>
        {
            
            if(tableauDeJournee[i].classList.contains('hidden'))
            {
                tableauDeJournee[i].classList.remove('hidden')
                journees[i].classList.add('hidden')
                return
            } 
            tableauDeJournee[i].classList.add('hidden')
            journees[i].classList.remove('hidden')
        })

        btnPiscine.addEventListener('click', ()=>
        {
            if(pannels[i].classList.contains('hidden'))
            {
                pannels[i].classList.remove('hidden')
                return
            } 
            pannels[i].classList.add('hidden')
        })

        btnDetail.addEventListener('click', () =>
        {
            
            if(tableauDeJournee[i].classList.contains('hidden'))
            {
                tableauDeJournee[i].classList.remove('hidden')
                journees[i].classList.add('hidden')
                return
            } 
            tableauDeJournee[i].classList.add('hidden')
            journees[i].classList.remove('hidden')

        })

        piscineEnfant.addEventListener('change', (e) =>
        {
            let sommeJournee = document.querySelectorAll('.sommeJournee')
            let ligne = document.querySelector('.piscine_enfant' + i)
            console.log(sommeJournee[i])
            let ligneSomme = sommeJournee[i]
            let somme = ligneSomme.children[1].innerHTML
            let prixTableaux = prixBasique[i]
            // let prixDeBase = prixTableaux.children[1].innerHTML
    
            let prixPrecedent = sommeJournee[i].getAttribute('data-previous')
            console.log(somme)
            if(ligne)
            {
                ligne.remove()
            }
            const quantiteEnfant = piscineEnfant.value
            ancienneValeur = quantiteEnfant
            const label = 'Piscine enfant'
            const prixEnfant = piscineEnfant.getAttribute('data-piscine-enfant')
            const prix = prixEnfant
            ligneSomme.remove()
            ajouterLigneTab(label, prix * quantiteEnfant, tBodies[i],'piscine_enfant' + i, quantiteEnfant)
            console.log(e);
            if(quantiteEnfant > 0)
            {
                if(ancienneValeur > quantiteEnfant)
                {
                   ajouterLigneTab('TOTAL journee',parseFloat( prixPrecedent) - parseFloat(prix), tBodies[i], 'sommeJournee' )
                   return
                }
                ajouterLigneTab('TOTAL journee',parseFloat( prixPrecedent) + parseFloat(prix), tBodies[i], 'sommeJournee' )
                return
            }
        })

        piscineAdulte.addEventListener('change', (e) =>
        {
           
            let sommeJournee = document.querySelectorAll('.sommeJournee')
            let ligne = document.querySelector('.piscine_adulte' + i)
            let ligneSomme = sommeJournee[i]
            let somme = ligneSomme.children[1].innerHTML
            let prixTableaux = prixBasique[i]
            // let prixDeBase = prixTableaux.children[1].innerHTML
            let prixPrecedent = sommeJournee[i].getAttribute('data-previous')
            if(ligne)
            {
                    ligne.remove()
            }
            
            const quantiteAdulte = piscineAdulte.value
            
            const label = 'Piscine adulte'
            const prixAdulte = piscineAdulte.getAttribute('data-piscine-adulte')
            const prix = prixAdulte
            ligneSomme.remove()
            ajouterLigneTab(label, prix,  tBodies[i], 'piscine_adulte' + i, quantiteAdulte)
            console.log(ancienneValeur);
            if(quantiteAdulte > 0 )
            {
                if(ancienneValeur > quantiteAdulte)
                {
                    ajouterLigneTab('TOTAL journee',parseFloat(prixPrecedent) - parseFloat( prix), tBodies[i], 'sommeJournee' )
                    ancienneValeur = quantiteAdulte
                    return
                }
                ajouterLigneTab('TOTAL journee',parseFloat(prixPrecedent) + parseFloat( prix), tBodies[i], 'sommeJournee' )
                ancienneValeur = quantiteAdulte
                return
            }
            
            // ajouterLigneTab('TOTAL journee',  parseFloat(somme) , tBodies[i], 'sommeJournee' )
        })

    }


})