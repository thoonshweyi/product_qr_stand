console.log("product print js");

// document.querySelectorAll('.sheet-description').forEach(function (container) {

//     const lines = [
//         ...container.querySelectorAll('.description-line')
//     ];

//     // container.innerHTML = '';

//     let lineCount = 0;
//     const descriptionLineLimit = Number(
//         container.dataset.descriptionLineLimit
//     );
//     // console.log(descriptionLineLimit);
//     for (const line of lines) {
//         console.log(line);

//         line.textContent = line.textContent.trim();
//         line.style.whiteSpace = 'normal';

//         console.log(line.scrollWidth , line.clientWidth);
//         if (line.scrollWidth > line.clientWidth) {
//             console.log('ဒီ line က မဆံ့ပါ:', line.textContent);
//             lineCount += 1;
//         }
//         lineCount += 1;

//         if(lineCount > descriptionLineLimit){
//             line.style.display = "none"
//         }
//     }
// });

// document.querySelectorAll('.description-line').forEach(function (line) {
//     if (line.scrollWidth > line.clientWidth) {
//         line.classList.add('truncated');

//         console.log('ဒီ line က မဆံ့ပါ:', line.textContent);
//     }
// });

document.querySelectorAll('.sheet-description').forEach(function (container) {
    const lines = [
        ...container.querySelectorAll('.description-line')
    ];

    let lineCount = 1;
    const descriptionLineLimit = Number(
        container.dataset.descriptionLineLimit
    );
    lines.forEach(function (line) {
        console.log(lineCount , descriptionLineLimit)
        if(lineCount > descriptionLineLimit){
            // line.style.display = 'none';
            line.remove();
            return;
        }

        lineCount++;
        if (line.scrollWidth > line.clientWidth) {
            console.log('ဒီ line က မဆံ့ပါ:', line.textContent);
            lineCount += 1;
        }

        line.textContent = line.textContent.trim();
        line.style.whiteSpace = 'normal';
    });
});