const form = document.querySelector("form");
const spotForError = document.querySelector('.submit-button');
const linebreak = document.createElement("br");

const errorObject = document.createElement('p');
errorObject.setAttribute('class', "alert alert-danger light mt-15 mb-0");

function removeErrorMessage () {
    spotForError.removeChild(linebreak);
    spotForError.removeChild(errorObject);
}

function generateError (errorMessage) {
    errorObject.textContent = errorMessage;
    spotForError.appendChild(linebreak);
    spotForError.appendChild(errorObject);
    window.addEventListener('click', removeErrorMessage, { once: true });
}

// two names have to be different
function checkNameValidity(event) {
    const player1_id = document.querySelector("input[name='player1_id']").value;
    const player2_id = document.querySelector("input[name='player2_id']").value;

    if (player1_id === player2_id) {
        event.preventDefault();
        generateError('Two players cannot be the same person.');
    }
}

//at least one score has to be >=21 and if both are >21, has to be a 2 point difference between two scores.
function checkScoreValidity (event) {
    checkNameValidity(event);
    const score1 = document.querySelector("input[name='score1']").value;
    const score2 = document.querySelector("input[name='score2']").value;
    const lost_score = Math.min(score1, score2);
    const won_score = Math.max(score1, score2);
    const difference = won_score - lost_score;
    if (lost_score < 0) {
        event.preventDefault();
        generateError('Scores have to be over 0.');
    } else if (won_score < 21) {
        event.preventDefault();
        generateError('At least one of the scores has to be over 21.');
    } else if (difference < 2) {
        event.preventDefault();
        generateError('The difference between scores has to be at least 2');
    } else if (won_score > 21 && difference > 2) {
        event.preventDefault();
        generateError('The difference between these scores has to be 2');
    }
}

form.addEventListener('submit', checkScoreValidity);
