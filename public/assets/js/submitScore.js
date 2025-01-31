const form = document.querySelector("form");

const spotForError = document.querySelector('#spot-for-error');
const linebreak = document.createElement("br");
const errorObject = document.createElement('p');
errorObject.setAttribute('class', "alert alert-danger light mt-15 mb-0");
function removeErrorMessage (event) {
    if (event.target.id !== "update_totals") {
        spotForError.removeChild(linebreak);
        spotForError.removeChild(errorObject);
    }
}
function generateError (errorMessage) {
    errorObject.textContent = errorMessage;
    spotForError.appendChild(linebreak);
    spotForError.appendChild(errorObject);
    setTimeout(() => {
        window.addEventListener('click', removeErrorMessage, { once: true });
    }, 100);
}

// two names have to be different
function checkNameValidity(event) {
    const player1_id = document.querySelector("input[name='player1_id']").value;
    const player2_id = document.querySelector("input[name='player2_id']").value;
    if (player1_id === "" || player2_id === "" || player1_id === player2_id) {
        generateError('The player names are required and cannot be the same person.');
        return false;
    }
    document.querySelector("input[name='player1_name']").disabled = true;
    document.querySelector("input[name='player2_name']").disabled = true;
    return true;
}

const numScores = 0;
const addRound = document.getElementById("add_round");
document.getElementById("update_totals").addEventListener('click', function (event) {
    const nameValid = checkNameValidity(event);
    if (nameValid) {
        let totals = [0, 0];
        let validInput = true;
        document.querySelectorAll(".player_scores_div").forEach((playerDiv, index) => {
            let label = playerDiv.querySelector("label");
            totals[index] = parseInt(label.textContent.match(/Total - (\d+)/)[1]);
            playerDiv.querySelectorAll('input[type="number"]').forEach(input => {
                input.classList.remove("is-invalid");
                if (input.value.trim() === "" || parseInt(input.value) < 0) {
                    input.classList.add("is-invalid");
                    validInput = false;
                } else {
                    totals[index] += parseInt(input.value);
                }
            });
        });
        if (!validInput) {
            generateError("All score fields must be filled and non-negative to update totals.");
            return;
        } else if ((totals[0] >= 271 || totals[1] >= 271) && totals[0] !== totals[1]) {
            form.submit();
            return;
        }

        document.querySelectorAll(".player_scores_div").forEach((div, index) => {
            let label = div.querySelector("label");
            let newTotal = parseInt(label.textContent.match(/Total - (\d+)/)[1]);

            div.querySelectorAll('input[type="number"]').forEach(input => {
                // Hide the original input
                input.style.display = "none";
                input.setAttribute("type", "hidden");

                // Extract the current name and index
                const nameMatch = input.name.match(/(player\d+_scores)\[(\d+)\]/);
                if (nameMatch) {
                    const baseName = nameMatch[1]; // e.g., "player1_scores"
                    const newIndex = parseInt(nameMatch[2]) + 3; // Increment index by 3

                    // Create a new input with the updated name
                    const newInput = document.createElement("input");
                    newInput.type = "number";
                    newInput.name = `${baseName}[${newIndex}]`;
                    newInput.min = "0";
                    newInput.style.width = "7rem";
                    newInput.style.display = "inline-block";
                    newInput.className = "text-center form-control";

                    // Append the new input after the hidden one
                    label.appendChild(newInput);

                    // Update the total
                    let value = input.value.trim();
                    newTotal += parseInt(value);
                }
            });
            let remaining = 271 - newTotal;
            if (remaining < 0) {
                remaining = 1;
            }
            const totalSpan = document.getElementById(`total-score${index + 1}`);
            totalSpan.innerHTML = `${newTotal} <span style="color: rgb(220, 58, 17);">(${remaining} to go)</span>`;
        });
    }
});
