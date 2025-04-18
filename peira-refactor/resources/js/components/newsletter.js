import tippy from 'tippy.js';

export default function subscribenews() {
    const input = document.getElementById("adressfield");
    const inputname = document.getElementById("namefield");
    const inputnachname = document.getElementById("nachnamefield");
    const checkbox = document.getElementById("postAgree");

    const tooltip = tippy(input, {
        content: 'Gib eine gültige E-Mailadresse an.',
        trigger: 'manual',
        placement: 'bottom'
    });

    const tooltipname = tippy(inputname, {
        content: 'Gib einen Vornamen an.',
        trigger: 'manual',
        placement: 'bottom'
    });

    const tooltipnachname = tippy(inputnachname, {
        content: 'Gib einen Nachnamen an.',
        trigger: 'manual',
        placement: 'bottom'
    });

    const tooltipcheckbox = tippy(checkbox, {
        content: 'Bitte stimme zu.',
        trigger: 'manual',
        placement: 'bottom'
    });

    const value = input.value.trim();
    const Name = inputname.value.trim();
    const Surname = inputnachname.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!value || !Name || !Surname || !checkbox.checked) {
        if (!value) tooltip.show();
        if (!Name) tooltipname.show();
        if (!Surname) tooltipnachname.show();
        if (!checkbox.checked) tooltipcheckbox.show();
        return;
    }

    if (!emailRegex.test(value)) {
        tooltip.show();
        return;
    }

    tooltip.hide();

    const newNode = {
        "data": {
            "type": "node--newsletteranmeldung",
            "attributes": {
                "title": Name,
                "field_nachanme": Surname,
                "field_e_mailadresse": value,
                "field_name_email": Name
            }
        }
    };

    fetch('/web/session/token')
        .then(res => res.text())
        .then(token => {
            return fetch('/web/api/json/node/newsletteranmeldung', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/vnd.api+json',
                    'Authorization': 'Basic ' + btoa('apiuser:Preia2023'),
                    'X-CSRF-Token': token
                },
                body: JSON.stringify(newNode)
            });
        })
        .then(res => res.json())
        .then(() => {
            const label = document.getElementById("labelnews");
            label.classList.add("labelnewsaltered");
            label.textContent = "Danke für die Anmeldung!";
        })
        .catch(error => {
            console.error("Newsletter-Anmeldung fehlgeschlagen:", error);
        });
}