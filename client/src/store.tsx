import React, { Dispatch, createContext, useReducer } from "react"
import { payloadType, globalState, children } from "./types"
import { decryptData, encryptData } from "./helpers";

const appState: globalState = {
    user:localStorage.getItem(encryptData('user')),
    isLoading: false,
    isError: false,
    isDisabled: false,
    notification: ''
}


export const globalContext = createContext<{state: globalState;
    dispatch: Dispatch<payloadType>;}>({state:appState, dispatch: () => null})

const reducer = (state: globalState, payload: payloadType): globalState => ({...state,...payload})

export const Store = ({ children }:children) => {
    const [state, dispatch] = useReducer(reducer, appState)
    
    return (
        <globalContext.Provider value={{state, dispatch}}>
            {children}
        </globalContext.Provider>
    )
}