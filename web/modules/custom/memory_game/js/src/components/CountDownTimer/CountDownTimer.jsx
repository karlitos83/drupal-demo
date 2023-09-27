import React, { useEffect, useState } from "react";
import { PostFetch } from '../helpers/helpers';

const CountDownTimer = ({gameTime, onComplete, state, token}) => {

  const { hours = 0, minutes = 0, seconds = 60 } = {minutes: gameTime, seconds: 0};
  const [[hrs, mins, secs], setTime] = useState([hours, minutes, seconds]);
  const reset = () => setTime([parseInt(hours), parseInt(minutes), parseInt(seconds)]);

  const [timerState, setTimerState] = useState(state);

  const tick = () => {
    if (hrs === 0 && mins === 0 && secs === 0) {
      onComplete();
      PostFetch({url: '/memory-game-over-by-time', token: token, body:{"completed": false}})
      .then((res)=> {
        return res
      });
    }
    else if (mins === 0 && secs === 0) {
      setTime([hrs - 1, 59, 59]);
    } else if (secs === 0) {
      setTime([hrs, mins - 1, 59]);
    } else {
      setTime([hrs, mins, secs - 1]);
    }
  };

  useEffect(() => {
    if(timerState === 'started') {
      const timerId = setInterval(() => tick(), 1000);
      return () => clearInterval(timerId);
    }
  });

  useEffect(()=> {
    setTimerState(state);
  },[state])

  return (
    <div className="memory-game__timer">
      <div>{`${mins.toString()}:${secs.toString().padStart(2, '0')}`}</div>
    </div>
  );

}

export default CountDownTimer;
