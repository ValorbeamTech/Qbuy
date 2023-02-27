import React from "react"

export type loginInputsType = {
    username: string
    password: string
}

export type serverResponse = {
    success: boolean
    message: any
}

export type globalState = {
    user: any
    isLoading: boolean
    isError: boolean
    isDisabled: boolean
    notification: string
}

export type children = React.Node

export type payloadType = Partial<globalState> & {}
