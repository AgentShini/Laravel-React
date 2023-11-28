import Files from "@/Components/Files";
import Header from "@/Components/Header";
import Upload from "@/Components/Upload";
export default function FileDashboard(){
return(
    <div>
    <div>
    <Header/>
    </div>
    <div className="bg-gray-600">
        <Upload/>
    <Files/> 
    </div>
    </div>
) 
   
    
}