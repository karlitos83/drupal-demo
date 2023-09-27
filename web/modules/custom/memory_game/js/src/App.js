import React, {useEffect, useState} from 'react';
import MemoryGameBuilder from "./components/GameBuilder/GameBuilder";
import CountDownTimer from "./components/CountDownTimer/CountDownTimer";
import {PostFetch, onGameOver, redirect} from "./components/helpers/helpers";

function App() {
  const [sessionToken, setSessionToken] = useState('');
  const [gameSettings, setGameSettings] = useState();
  const [appState, setAppState] = useState('standby');


  // Set session token
  useEffect(()=> {
    fetch('/session/token')
      .then(res => res.text()).then(token => setSessionToken(token));
  },[])

  // Get game settings
  useEffect(()=> {
   if(sessionToken) {
     fetch('/memory-game-settings')
       .then(response => {
         if(response.ok) {
           return response.json()
         } else {
           redirect({path: '/acceso-denegado'})
         }
       }).then(response => {
       response.webform_id
         ? setGameSettings(response)
         : redirect({path: '/acceso-denegado'})
     }).catch((error) => {
       redirect({path: '/acceso-denegado'})
     });
   }
  },[sessionToken]);

  return gameSettings
    ? <div className="app-memory-container">
        <CountDownTimer gameTime={gameSettings.game_time} onComplete={()=> onGameOver({redirect:gameSettings.no_winner_redirect_path, callback: () => setAppState('gameOver')})} state={appState} token={sessionToken}/>
        <MemoryGameBuilder state={appState} token={sessionToken} gameSettings={gameSettings} callback={()=>setAppState('completed')}/>
        {
          appState === "standby" &&
          <div className="memory-game__start-wrapper">
            <button onClick={(e)=> {
              e.preventDefault();
              PostFetch({url: '/memory-start-game', token: sessionToken, body:{"start": true}})
                .then(()=> {
                  setAppState('started');
                });
            }} className="start-button">¡Comenzar!</button>
          </div>
        }
      </div>
    : <div>Loading game</div>

}

export default App;
