import React from 'react'
import { useContext } from 'react'
import { useState } from 'react'
import { globalContext } from '../store'
import { loginInputsType, serverResponse } from '../types'
import { Loader } from '../components'
import { decryptData, encryptData } from '../helpers'

const Login = () => {
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')
  const [errorMessage, setErrorMessage] = useState('username or password error!')

  const { state, dispatch } = useContext(globalContext)


  // reusable input change
  const handleInputChange = (event: React.ChangeEvent<HTMLElement>): void => {
    try {
      // get HTML element name and value from event target
      const { name, value }: any = event.target
      if (name === 'username') {
        setUsername(value)
      }

      if (name === 'password') {
        setPassword(value)
      }

      if (value === '') {
        dispatch({ isLoading: false })
      } else {
        dispatch({ isError: false })
      }
      // update application state
      // dispatch({ [name]:value, isDisabled:false })

    } catch (error) {
      console.log((error as Error).message)
      dispatch({ "notification": (error as Error).message })
    }
  }



  // validation
  let errors = []
  const loginInputs: loginInputsType = { username: username, password: password }
  function validateInputs(loginInputs: loginInputsType) {
    if (loginInputs.username === '' || loginInputs.password === '') {
      errors.push('')
      dispatch({ isDisabled: true })
      dispatch({ isError: true })
      return false
    }
  }

  // submission
  async function submitForm(e: any) {
    e.preventDefault()
    validateInputs(loginInputs)

    dispatch({ isLoading: true })
    const headers = new Headers({
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + JSON.parse(decryptData(state.user)).auth_token
    })

    const data: serverResponse = await (await fetch('http://localhost:8000/endpoints/auth/user',
      {
        mode: 'cors',
        method: "POST",
        body: JSON.stringify(loginInputs),
        headers: headers
      },
    )).json()

    console.log(data.message)

    if (!data.success && (data.message !== '')) {
      dispatch({ isLoading: false })
      dispatch({ isDisabled: true })
      dispatch({ isError: true })
      setErrorMessage(data.message)
    }

    if (!data.success && (data.message === '')) {
      dispatch({ isLoading: false })
      dispatch({ isDisabled: true })
      dispatch({ isError: true })
      setErrorMessage('something went wrong!')

    }

    if (data && data.success) {
      dispatch({ isLoading: false })
      dispatch({ isDisabled: false })
      dispatch({ notification: "success, bounce in!" })
      localStorage.setItem(encryptData('user'), encryptData(JSON.stringify(data.message)))
      
    }
    
  }

  return (
    <React.Fragment>
      <div className="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
        <div className="py-3 sm:max-w-xl sm:mx-auto">
  
          <div className="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <img className="w-6/12 mx-auto mb-10" src="https://logos-world.net/wp-content/uploads/2020/04/Huawei-Logo.png" alt="pro" />
            <div className="max-w-md mx-auto">
              <div>
                <h1 className="text-2xl font-semibold text-center">Login Form | Welcome</h1>
              </div>
              <form onSubmit={submitForm}>
                <div className="divide-y divide-gray-200">
                  <div className="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                    <div className="relative">
                      <input
                        autoComplete="off"
                        name="username"
                        type="text"
                        onChange={handleInputChange}
                        className="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600 px-2 rounded-md" placeholder="Email address" />
                    </div>
                    <div className="relative">
                      <input
                        autoComplete="off"
                        name="password"
                        type="password"
                        onChange={handleInputChange}
                        className="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600 px-2 rounded-md" placeholder="Password" />
                    </div>
                    <div className="relative">
                      <button type='submit' className="bg-blue-500 rounded-md px-2 py-1 w-full uppercase text-white" disabled={state.isDisabled}>{
                        state.isLoading ? (<Loader />) : "login"}</button>
                    </div>
                    {state.isError ? (<div className='bg-red-500 text-white p-2 text-center rounded-md'>{errorMessage}</div>) : ''}
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </React.Fragment>
  )
}




























export default Login