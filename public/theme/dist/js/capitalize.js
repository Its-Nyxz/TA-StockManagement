function capitalizeAfterPeriod(text) {
    if (text === null) {
        return '-';
    }
    
    return text.replace(/(?:^|\. +)(\w)/g, function(match, letter) {
        return (match[0] === '.' ? '.' : '') + letter.toUpperCase();
    });
}