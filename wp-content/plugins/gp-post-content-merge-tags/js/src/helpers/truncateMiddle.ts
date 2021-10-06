export default function truncateMiddle( text: string, length: number ) {

    if( ! text || text.length <= length ) {
        return text;
    }

    var halfLength = length / 2;

    return text.substr( 0, halfLength ) + '...' + text.substr( text.length - ( halfLength - 1 ), halfLength );
}