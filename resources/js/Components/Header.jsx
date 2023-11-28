import { Disclosure } from '@headlessui/react'


export default function Header() {
  return (
    <>
      <div className="min-h-full bg-gray-900 ">
        <Disclosure as="nav">
          {({ open }) => (
            <>
              <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex h-16 items-center justify-between">
                <div className="flex items-center">
  <div className="flex-shrink-0">
    <img
      className="h-10 w-10"
      src="https://img.logoipsum.com/289.svg"
      alt="Your Company"
    />
  </div>
  <div className="ml-2 text-gray-300 text-2xl">Edupeak</div>
</div>

              </div>
              </div>

           
            </>
          )}
        </Disclosure>

        <header className="shadow bg-gray-500 ">
          <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 className="text-3xl font-bold tracking-tight text-gray-300">Files and Assets</h1>
            <h4 className = "font-bold tracking-tight text-gray-300">Upload your files below</h4>
          </div>
        </header>
        
      </div>
    </>
  )
}
