// Event listener for form submission
document.getElementById('event-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    // Append visibility value
    const visibility = document.querySelector('.visibility-btn.selected').getAttribute('data-value');
    formData.append('visibility', visibility);

    // Fetch API to send data to PHP
    fetch('../php/create_event_poll.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.text())
        .then((data) => {
            alert(data); // Display server response
            if (data.includes("successfully")) {
                document.getElementById('event-form').reset(); // Reset form on success
                // Hide private users input if it was shown
                document.getElementById('private-users').style.display = 'none';
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
});

// Add dynamic date/time options
document.getElementById('add-datetime-btn').addEventListener('click', function () {
    const container = document.getElementById('datetime-options');
    const inputGroup = document.createElement('div');
    inputGroup.classList.add('datetime-option-group');

    const datetimeInput = document.createElement('input');
    datetimeInput.type = 'datetime-local';
    datetimeInput.required = true;

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'remove-datetime-btn';
    removeBtn.textContent = 'Remove';
    removeBtn.classList.add('remove-datetime-btn');

    // Remove date/time option
    removeBtn.addEventListener('click', function () {
        container.removeChild(inputGroup);
    });

    inputGroup.appendChild(input);
    inputGroup.appendChild(removeBtn);
    container.appendChild(inputGroup);
});

// Toggle visibility options
document.querySelectorAll('.visibility-btn').forEach((btn) => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.visibility-btn').forEach((b) => b.classList.remove('selected'));
        this.classList.add('selected');

        const visibilityValue = this.getAttribute('data-value');
        // Show/hide private users input based on visibility
        document.getElementById('private-users').style.display = visibilityValue === 'private' ? 'block' : 'none';
    });
});
