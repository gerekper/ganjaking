export const stripHTML = (string) => {
    return string.replace( /(<([^>]+)>)/ig, '');
}