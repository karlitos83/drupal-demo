import React, {useEffect, useState} from 'react';
import CountDownTimer from '../CountDownTimer/CountDownTimer';
import { PostFetch, redirect } from '../helpers/helpers';


/**
 * Helper function to build the cards for memory game.
 *
 * @param {string} state:
 *   The game status
 * @param {number} token:
 *   The session token number.
 * @param {object} gameSettings:
 *   Game settings configurations from drupal.
 * @param {function} callback:
 *   A function to be executed on game end.
 * @returns {JSX.Element}
 */
export default function GameBuilder({state, token, gameSettings,callback}) {
  const [gameStatus, setGameStatus] = useState(state);
  const [cardsData, setCardsData] = useState();
  const [cardsFlipped, setCardsFlipped] = useState(0);
  const [cardsSelected_1, setCardsSelected_1] = useState();
  const [cardsSelected_2, setCardsSelected_2] = useState();
  const [messageActive, setMessageActive] = useState(false);
  const [gameMessage, setGameMessage] = useState();
  let bgImage = (gameSettings.card_background).replace('public:/', '/sites/default/files');

  // Card construction.
  useEffect(()=> {
    let cardPositions = gameSettings.cards_number;
    let cardsInit = {};

    // Create Cards Data Object based on the
    for (let i = 0; i < cardPositions; i++) {
      cardsInit[`card_${i}`] = {
        'position': i,
      }
    }
    setCardsData(cardsInit);
  },[]);

  useEffect(()=>{
    setGameStatus(state);
  },[state])

  useEffect(()=> {
    // Get first card data.
    if (cardsFlipped === 1) {
      PostFetch({url: '/memory-flip-first-card', token: token, body:{"card1": cardsSelected_1}})
        .then((gameData) => {
        let card1 = document.getElementById(`memoryCard_${cardsSelected_1}`);
        let card1_bg = (gameData[cardsSelected_1]).replace('public://', '/sites/default/files/');
        card1.querySelector('.memory-card__back').style.backgroundImage = `url('${card1_bg}')`;
      });
    }

    // Get data
    if(cardsFlipped === 2) {
      PostFetch({url: '/memory-flip-cards', token: token, body:{"card1": cardsSelected_1, "card2": cardsSelected_2}})
      .then((gameData) => {
        /** set cards bg on flip **/
        let card1 = document.getElementById(`memoryCard_${cardsSelected_1}`);
        let card2 = document.getElementById(`memoryCard_${cardsSelected_2}`);
        let card2_bg = (gameData[cardsSelected_2]).replace('public://', '/sites/default/files/');
        card2.querySelector('.memory-card__back').style.backgroundImage = `url('${card2_bg}')`;
        /** ======== **/
        if(gameData.isPair && gameData.completed === 0) {
          setCardsFlipped(0)
          setGameMessage('¡Has encontrado un par!');
          setMessageActive(true);

        } else if(gameData.completed === 1 ) {
          setGameStatus('complete');
          setGameMessage('¡Has completado el juego!');
          setMessageActive(true);
          callback();
          // Redirect to winner page.
          redirect({path: gameSettings.winner_redirect_path, timeOut: 2000})
        }
        else {
          setTimeout(()=>{
            card1.classList.remove('flipped');
            card2.classList.remove('flipped');
            setCardsFlipped(0)
          }, 1000)

          setGameMessage('Ups!..Vuelve a intentarlo');
          setMessageActive(true);
        }
      });
    }
  },[cardsFlipped])

  // Hide Game Message after 2secs.
  useEffect(()=> {
    if(messageActive && gameStatus === 'started') {
      setTimeout(()=> setMessageActive(false), 1000)
    }

    if(gameStatus === 'gameOver') {
      setMessageActive(true);
      setGameMessage('Se terminó el juego :(')
    }
  },[messageActive, gameStatus])

  const flipCard = (e) => {
    if(cardsFlipped < 2) {
      let card = (e.target).closest('.memory-card');

      if(!(card.classList.contains('flipped'))) {
        card.classList.add('flipped')
        setCardsFlipped(cardsFlipped + 1 );
      }

      if (cardsFlipped === 0) return setCardsSelected_1(card.getAttribute('data-index'));
      setCardsSelected_2(card.getAttribute('data-index'));
    }
  }

  return (
    <div className="memory-game-wrapper">
      <div className="memory-game__intro">
        Encuentra los pares dando click en cada tarjeta.
      </div>
      {
        cardsData &&
        <div className='cards-wrapper'>
          {
            Object.keys(cardsData).map(cardKey => {
              let cardData = cardsData[cardKey];
               return (
                 <div
                   key={cardData.position}
                   id={`memoryCard_${cardData.position}`}
                   className={`memory-card card--${cardData.position} active`}
                   onClick={flipCard}
                   data-index={cardData.position}
                 >
                   <div className="memory-card-inner">
                     <div className="memory-card__front" style={{backgroundImage: `url(${bgImage})`}}></div>
                     <div className="memory-card__back"></div>
                   </div>
                 </div>
               )
            })
          }
        </div>
      }
      {
        messageActive &&
        <div className="memory-game__massage">{gameMessage}</div>
      }
    </div>
  );
}

