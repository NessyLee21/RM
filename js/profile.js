function editPhoto() {
    const photoInput = document.createElement('input');
    photoInput.type = 'file';
    photoInput.accept = 'image/*';
    photoInput.onchange = () => {
        const formData = new FormData();
        formData.append('photo', photoInput.files[0]);

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('profilePhoto').src = data.newPhoto;
                } else {
                    alert(data.error);
                }
            });
    };
    photoInput.click();
}

function editDescription() {
    const description = document.getElementById('profileDescription');
    const oldText = description.textContent;
    const input = document.createElement('textarea');
    input.value = oldText;
    input.onblur = () => {
        fetch('update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ description: input.value })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    description.textContent = data.newDescription;
                } else {
                    alert(data.error);
                }
            });
        description.replaceWith(input);
    };
    description.replaceWith(input);
    input.focus();
}
