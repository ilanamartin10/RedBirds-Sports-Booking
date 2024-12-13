// JavaScript for Event Polling Page

// Toggle Private Users Input based on visibility buttons
const visibilityButtons = document.querySelectorAll('.visibility-btn');
const privateUsersDiv = document.getElementById('private-users');
let visibility = 'public'; // default value

visibilityButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove 'selected' class from all buttons
        visibilityButtons.forEach(btn => btn.classList.remove('selected'));
        // Add 'selected' class to clicked button
        button.classList.add('selected');
        // Update visibility value
        visibility = button.getAttribute('data-value');
        // Toggle private users input
        if (visibility === 'private') {
            privateUsersDiv.style.display = 'block';
        } else {
            privateUsersDiv.style.display = 'none';
        }
    });
});

// Add Date/Time Options
const addDatetimeBtn = document.getElementById('add-datetime-btn');
const datetimeOptionsDiv = document.getElementById('datetime-options');

addDatetimeBtn.addEventListener('click', addDatetimeOption);

function addDatetimeOption() {
    const datetimeOptionDiv = document.createElement('div');
    datetimeOptionDiv.className = 'datetime-option';

    const datetimeInput = document.createElement('input');
    datetimeInput.type = 'datetime-local';
    datetimeInput.required = true;

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'remove-datetime-btn';
    removeBtn.textContent = 'Remove';
    removeBtn.addEventListener('click', () => {
        datetimeOptionsDiv.removeChild(datetimeOptionDiv);
    });

    datetimeOptionDiv.appendChild(datetimeInput);
    datetimeOptionDiv.appendChild(removeBtn);
    datetimeOptionsDiv.appendChild(datetimeOptionDiv);
}

// Handle Form Submission
const eventForm = document.getElementById('event-form');
const pollDisplaySection = document.getElementById('poll-display-section');

eventForm.addEventListener('submit', (e) => {
    e.preventDefault();
    createEventPoll();
});

function createEventPoll() {
    // Collect Event Details
    const eventTitle = document.getElementById('event-title').value;
    const eventDescription = document.getElementById('event-description').value;
    const eventLocation = document.getElementById('event-location').value;
    const maxParticipants = document.getElementById('max-participants').value || 'Unlimited';
    const privateEmails = document.getElementById('private-emails').value;

    // Collect Date & Time Options
    const datetimeInputs = datetimeOptionsDiv.querySelectorAll('input[type="datetime-local"]');
    const datetimeOptions = [];
    datetimeInputs.forEach(input => {
        if (input.value) {
            datetimeOptions.push(input.value);
        }
    });

    if (datetimeOptions.length === 0) {
        alert('Please add at least one date/time option.');
        return;
    }

    // Display Poll Details
    document.getElementById('poll-title').textContent = eventTitle;
    document.getElementById('poll-description').textContent = eventDescription;
    document.getElementById('poll-location').textContent = `Location: ${eventLocation}`;
    document.getElementById('poll-creator').textContent = `Created by: You`;

    const votingOptionsDiv = document.getElementById('voting-options');
    votingOptionsDiv.innerHTML = '';

    const voteResultsDiv = document.getElementById('vote-results');
    voteResultsDiv.innerHTML = '';

    datetimeOptions.forEach(option => {
        const optionButton = document.createElement('button');
        optionButton.type = 'button';
        optionButton.textContent = new Date(option).toLocaleString();
        optionButton.dataset.option = option;
        optionButton.addEventListener('click', () => voteForOption(option));

        votingOptionsDiv.appendChild(optionButton);

        // Initialize Vote Counts
        const voteResultItem = document.createElement('div');
        voteResultItem.className = 'vote-result-item';

        const optionText = new Date(option).toLocaleString();

        const voteCountSpan = document.createElement('span');
        voteCountSpan.className = 'vote-count';
        voteCountSpan.textContent = '0 votes';

        const progressBarContainer = document.createElement('div');
        progressBarContainer.className = 'progress-bar-container';

        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        progressBar.style.width = '0%';

        progressBarContainer.appendChild(progressBar);

        voteResultItem.appendChild(document.createTextNode(optionText));
        voteResultItem.appendChild(voteCountSpan);
        voteResultItem.appendChild(progressBarContainer);

        voteResultsDiv.appendChild(voteResultItem);
    });

    // Show Poll Display Section
    pollDisplaySection.style.display = 'block';

    // Scroll to Poll Display
    pollDisplaySection.scrollIntoView({ behavior: 'smooth' });
}

// Voting Functionality
const votes = {};
let totalVotes = 0;

function voteForOption(option) {
    if (!votes[option]) {
        votes[option] = 0;
    }
    votes[option] += 1;
    totalVotes += 1;

    // Update Vote Count Display
    const voteResultsDiv = document.getElementById('vote-results');
    const voteResultItems = voteResultsDiv.getElementsByClassName('vote-result-item');

    for (let item of voteResultItems) {
        const optionText = item.childNodes[0].nodeValue;
        const voteCountSpan = item.querySelector('.vote-count');
        const progressBar = item.querySelector('.progress-bar');

        // Match the option using the original option value
        const optionKey = datetimeOptionsDiv.querySelector(`input[value="${option}"]`)?.value || option;

        const count = votes[optionKey] || 0;
        const percentage = ((count / totalVotes) * 100).toFixed(2);

        voteCountSpan.textContent = `${count} votes`;
        progressBar.style.width = `${percentage}%`;
    }

    // Highlight Selected Option
    const votingButtons = document.getElementById('voting-options').getElementsByTagName('button');
    for (let btn of votingButtons) {
        if (btn.dataset.option === option) {
            btn.classList.add('selected');
        } else {
            btn.classList.remove('selected');
        }
    }
}
