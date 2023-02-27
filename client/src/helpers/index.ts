import { Buffer } from 'buffer'

export const encryptData = (text: string): string => {
    // plain-text string
    const str = text;

    // create a buffer
    const buff = Buffer.from(str, 'utf-8');

    // decode buffer as Base64
    const base64 = buff.toString('base64');

    // return Base64 string
    return base64;
}


export const decryptData = (text: string): string => {
    // Base64 encoded string
    const base64 = text;

    // create a buffer
    const buff = Buffer.from(base64, 'base64');

    // decode buffer as UTF-8
    const str = buff.toString('utf-8');

    // return normal string
    return str;
}