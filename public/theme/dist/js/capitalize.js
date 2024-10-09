function capitalizeAfterPeriod (text) {
    return text.replace(/(?:^|\. )\w/g, function(match) {
        return match.toUpperCase();
    });
};